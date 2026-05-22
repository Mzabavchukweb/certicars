<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtomotoImportController extends Controller
{
    public function scrape(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url', 'regex:/^https?:\/\/(www\.)?otomoto\.pl\//'],
        ]);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Cache-Control' => 'max-age=0',
            ])->timeout(15)->get($request->url);

            if (!$response->successful()) {
                $status = $response->status();
                $msg = match(true) {
                    $status === 404 => 'Ogłoszenie nie istnieje lub zostało usunięte (404)',
                    $status === 403 => 'Otomoto zablokował zapytanie — spróbuj ponownie za chwilę (403)',
                    $status >= 500 => 'Serwer Otomoto tymczasowo niedostępny (' . $status . ')',
                    default => 'Nie udało się pobrać strony Otomoto (HTTP ' . $status . ')',
                };
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            $html = $response->body();

            // PRIMARY: Extract __NEXT_DATA__ (Next.js SSR data)
            $data = $this->extractFromNextData($html);

            // FALLBACK: JSON-LD + HTML parsing
            if (empty($data['model']) && empty($data['brand'])) {
                $data = array_merge($data, $this->extractFromJsonLd($html));
                $data = array_merge($data, $this->extractFromHtml($html));
            }

            if (empty($data['model']) && empty($data['brand'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nie udało się odczytać danych z tego ogłoszenia. Sprawdź link.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Throwable $e) {
            Log::error('Otomoto import error', [
                'url'     => $request->url,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Błąd techniczny podczas importu. Spróbuj ponownie.',
            ], 500);
        }
    }

    private function extractFromNextData(string $html): array
    {
        $data = [];

        if (!preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
            return $data;
        }

        $nextData = json_decode($m[1], true);
        if (!$nextData) return $data;

        // Navigate to advert data - try multiple paths
        $advert = $nextData['props']['pageProps']['advert']
            ?? $nextData['props']['pageProps']['ad']
            ?? $nextData['props']['pageProps']['data']
            ?? null;

        if (!$advert) {
            // Try deeper search
            $pageProps = $nextData['props']['pageProps'] ?? [];
            foreach ($pageProps as $key => $val) {
                if (is_array($val) && (isset($val['parameters']) || isset($val['title']) || isset($val['price']))) {
                    $advert = $val;
                    break;
                }
            }
        }

        if (!$advert) return $data;

        // Title
        if (isset($advert['title'])) {
            $data['title'] = $advert['title'];
        }

        // Price
        if (isset($advert['price'])) {
            $price = $advert['price'];
            $data['price'] = is_array($price)
                ? (float)($price['amount'] ?? $price['value'] ?? 0)
                : (float)$price;
            if (is_array($price) && isset($price['currency'])) {
                $data['currency'] = $price['currency'];
            }
        }

        // Parameters — try parametersDict (new format), then parameters/params (old)
        $paramsDict = $advert['parametersDict'] ?? null;
        $params = $advert['parameters'] ?? $advert['params'] ?? [];

        $paramMap = [];

        if ($paramsDict && is_array($paramsDict)) {
            // New format: {make: {label: "make", values: [{value: "renault", label: "Renault"}]}}
            foreach ($paramsDict as $key => $entry) {
                if (!is_array($entry)) continue;
                $values = $entry['values'] ?? [];
                if (!empty($values) && is_array($values)) {
                    // Take label if available (human-readable), else value
                    $first = $values[0] ?? [];
                    $paramMap[strtolower($key)] = $first['label'] ?? $first['value'] ?? '';
                }
            }
        } elseif (is_array($params) && !empty($params)) {
            // Old format: [{key, value}] or flat map
            if (isset($params[0]) && is_array($params[0])) {
                foreach ($params as $p) {
                    $key = $p['key'] ?? $p['name'] ?? $p['label'] ?? '';
                    $val = $p['value'] ?? $p['displayValue'] ?? $p['display_value'] ?? '';
                    if ($key && $val) {
                        $paramMap[strtolower($key)] = $val;
                    }
                }
            } else {
                $paramMap = array_change_key_case($params, CASE_LOWER);
            }
        }

        if (!empty($paramMap)) {
            $this->mapParams($paramMap, $data);
        }

        // Equipment/features — new format: [{key, label, values: [{key, label}]}]
        $features = $advert['equipment'] ?? $advert['features'] ?? [];
        if (is_array($features) && !empty($features)) {
            $equipment = [];
            foreach ($features as $f) {
                if (is_string($f)) {
                    $equipment[] = $f;
                } elseif (is_array($f)) {
                    // New grouped format: {key, label, values: [{key, label}]}
                    if (isset($f['values']) && is_array($f['values'])) {
                        foreach ($f['values'] as $v) {
                            if (is_array($v) && isset($v['label'])) {
                                $equipment[] = $v['label'];
                            } elseif (is_string($v)) {
                                $equipment[] = $v;
                            }
                        }
                    } else {
                        $equipment[] = $f['name'] ?? $f['label'] ?? $f['value'] ?? '';
                    }
                }
            }
            $data['equipment'] = array_filter($equipment);
        }

        // Description
        if (isset($advert['description'])) {
            $data['description'] = is_string($advert['description'])
                ? strip_tags($advert['description'])
                : '';
        }

        // Step 3 — History: imported, country_origin
        $isImported = $paramMap['is_imported_car'] ?? null;
        if ($isImported) {
            $data['imported'] = (strtolower($isImported) === 'tak' || $isImported === '1') ? 1 : 0;
        }

        // Country from seller location
        $sellerLocation = $advert['seller']['location'] ?? [];
        if (!empty($sellerLocation['address'])) {
            // "Przecław, brzeziński, Łódzkie" — just note it as context
            $data['seller_location'] = $sellerLocation['address'];
        }

        // Use production_year as first_registration fallback
        if (!isset($data['first_registration']) && isset($data['production_year'])) {
            $data['first_registration'] = $data['production_year'];
        }

        // SEO auto-generate
        $brand = $data['brand'] ?? '';
        $model = $data['model'] ?? '';
        $version = $data['version'] ?? '';
        $year = $data['production_year'] ?? '';
        $powerHp = $data['power_hp'] ?? '';
        $fuelType = $data['fuel_type'] ?? '';
        if ($brand && $model) {
            $data['meta_title'] = trim("$brand $model $version $year — $powerHp KM | CertiCars");
            $data['meta_description'] = trim("Sprawdzone $brand $model $version z $year roku. $powerHp KM, $fuelType. Pełna historia serwisowa, raport CertiCheck.");
            $data['focus_keyword'] = trim("$brand $model $version");
        }

        return $data;
    }

    private function mapParams(array $params, array &$data): void
    {
        // Direct mapping for common keys
        $directMap = [
            'make' => 'brand', 'marka' => 'brand', 'marka_pojazdu' => 'brand',
            'model' => 'model', 'model_pojazdu' => 'model',
            'year' => 'production_year', 'rok' => 'production_year', 'rok_produkcji' => 'production_year',
            'mileage' => 'mileage', 'przebieg' => 'mileage',
            'engine_capacity' => 'engine_capacity', 'pojemnosc_skokowa' => 'engine_capacity', 'pojemność_skokowa' => 'engine_capacity',
            'engine_power' => 'power_hp', 'moc' => 'power_hp',
            'fuel_type' => 'fuel_type', 'rodzaj_paliwa' => 'fuel_type', 'paliwo' => 'fuel_type',
            'gearbox' => 'transmission', 'skrzynia_biegow' => 'transmission', 'skrzynia_biegów' => 'transmission',
            'transmission' => 'drive_type', 'drive' => 'drive_type', 'napęd' => 'drive_type', 'naped' => 'drive_type',
            'body_type' => 'body_type', 'typ_nadwozia' => 'body_type', 'nadwozie' => 'body_type',
            'color' => 'color', 'kolor' => 'color',
            'door_count' => 'doors', 'doors' => 'doors', 'liczba_drzwi' => 'doors',
            'nr_seats' => 'seats', 'seats' => 'seats', 'liczba_miejsc' => 'seats',
            'vin' => 'vin',
            'country_origin' => 'country_registration', 'kraj_pochodzenia' => 'country_registration',
            'nr_owner' => 'previous_owners', 'liczba_wlascicieli' => 'previous_owners',
            'co2_emissions' => 'co2_emission', 'emisja_co2' => 'co2_emission',
            'version' => 'version', 'version_label' => 'version',
            'generation' => 'generation',
            'colour_type' => 'color_type', 'color_type' => 'color_type',
            'date_registration' => 'first_registration',
        ];

        foreach ($directMap as $otomotoKey => $certiKey) {
            if (isset($params[$otomotoKey]) && !empty($params[$otomotoKey])) {
                $val = $params[$otomotoKey];
                $cleaned = $this->cleanValue($val, $certiKey);
                // Skip encrypted/hashed values (Otomoto masks VIN, registration dates etc)
                $strVal = (string)$cleaned;
                if (preg_match('/[\/=]{2,}|\.1\./', $strVal) || (strlen($strVal) > 30 && preg_match('/[A-Za-z0-9+\/=]{20,}/', $strVal))) {
                    continue;
                }
                $data[$certiKey] = $cleaned;
            }
        }
    }

    private function extractFromJsonLd(string $html): array
    {
        $data = [];

        if (!preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $jsonMatches)) {
            return $data;
        }

        foreach ($jsonMatches[1] as $jsonStr) {
            $json = json_decode(trim($jsonStr), true);
            if (!$json) continue;

            $items = isset($json['@graph']) ? $json['@graph'] : [$json];
            foreach ($items as $item) {
                $type = $item['@type'] ?? '';
                if (!in_array($type, ['Product', 'Car', 'Vehicle'])) continue;

                if (isset($item['brand'])) {
                    $data['brand'] = is_array($item['brand']) ? ($item['brand']['name'] ?? '') : $item['brand'];
                }
                if (isset($item['model'])) $data['model'] = $item['model'];
                if (isset($item['name'])) $data['title'] = $item['name'];
                if (isset($item['offers']['price'])) $data['price'] = (float)$item['offers']['price'];
                if (isset($item['mileageFromOdometer'])) {
                    $m = $item['mileageFromOdometer'];
                    $data['mileage'] = is_array($m) ? (int)($m['value'] ?? 0) : (int)$m;
                }
                if (isset($item['vehicleIdentificationNumber'])) $data['vin'] = $item['vehicleIdentificationNumber'];
                if (isset($item['color'])) $data['color'] = $item['color'];
                if (isset($item['fuelType'])) $data['fuel_type'] = $this->mapFuelType($item['fuelType']);
                if (isset($item['vehicleTransmission'])) $data['transmission'] = $this->mapTransmission($item['vehicleTransmission']);
                if (isset($item['productionDate'])) $data['first_registration'] = $item['productionDate'];
                if (isset($item['vehicleModelDate'])) $data['first_registration'] = $item['vehicleModelDate'];
            }
        }

        return $data;
    }

    private function extractFromHtml(string $html): array
    {
        $data = [];
        if (preg_match('/og:title["\'][^>]*content=["\']([^"\']+)/i', $html, $m)) {
            $data['title'] = html_entity_decode($m[1]);
        }
        return $data;
    }

    private function cleanValue($value, string $field)
    {
        if (is_array($value)) {
            $value = $value['displayValue'] ?? $value['display_value'] ?? $value['value'] ?? json_encode($value);
        }
        $value = trim((string)$value);

        switch ($field) {
            case 'mileage':
            case 'doors':
            case 'seats':
            case 'previous_owners':
                return (int)preg_replace('/\D/', '', $value);
            case 'engine_capacity':
                // "1968 cm3" or "1 968 cm3" — capture leading digits+spaces, strip spaces
                preg_match('/^([\d\s]+)/', $value, $m);
                return isset($m[1]) ? (int)preg_replace('/\s+/', '', $m[1]) : 0;
            case 'power_hp':
                preg_match('/(\d+)/', $value, $m);
                return isset($m[1]) ? (int)$m[1] : $value;
            case 'fuel_type':
                return $this->mapFuelType($value);
            case 'transmission':
                return $this->mapTransmission($value);
            case 'body_type':
                return $this->mapBodyType($value);
            case 'price':
                return (float)preg_replace('/[^\d.,]/', '', str_replace(',', '.', $value));
            default:
                return $value;
        }
    }

    private function mapFuelType(string $raw): string
    {
        $lower = mb_strtolower(trim($raw));
        $map = [
            'petrol' => 'Benzyna', 'gasoline' => 'Benzyna', 'benzyna' => 'Benzyna',
            'diesel' => 'Diesel', 'on' => 'Diesel',
            'hybrid' => 'Hybryda', 'hybryda' => 'Hybryda',
            'plug-in' => 'Hybryda plug-in', 'phev' => 'Hybryda plug-in',
            'electric' => 'Elektryczny', 'elektryczny' => 'Elektryczny',
            'lpg' => 'LPG', 'benzyna+lpg' => 'LPG',
            'cng' => 'CNG',
        ];
        return $map[$lower] ?? ucfirst($raw);
    }

    private function mapTransmission(string $raw): string
    {
        $lower = mb_strtolower(trim($raw));
        if (str_contains($lower, 'automat')) return 'Automatyczna';
        if (str_contains($lower, 'manual')) return 'Manualna';
        if (str_contains($lower, 'cvt')) return 'CVT';
        if (str_contains($lower, 'dsg') || str_contains($lower, 'dct')) return 'Półautomatyczna (DSG/DCT)';
        return ucfirst($raw);
    }

    private function mapBodyType(string $raw): string
    {
        $lower = mb_strtolower(trim($raw));
        $map = [
            'sedan' => 'Sedan', 'suv' => 'SUV', 'hatchback' => 'Hatchback',
            'kombi' => 'Kombi', 'estate' => 'Kombi', 'wagon' => 'Kombi',
            'coupe' => 'Coupé', 'cabrio' => 'Kabriolet',
            'van' => 'Van', 'minivan' => 'Minivan', 'pickup' => 'Pickup',
            'compact' => 'Kompakt',
        ];
        foreach ($map as $key => $val) {
            if (str_contains($lower, $key)) return $val;
        }
        return ucfirst($raw);
    }
}
