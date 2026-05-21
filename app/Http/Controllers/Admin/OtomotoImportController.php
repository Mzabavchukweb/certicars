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
                return response()->json([
                    'success' => false,
                    'message' => 'Nie udało się pobrać strony Otomoto (HTTP ' . $response->status() . ')',
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
            Log::error('Otomoto import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Błąd importu: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function extractFromNextData(string $html): array
    {
        $data = [];

        if (!preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $html, $m)) {
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

        // Parameters - the main data source
        $params = $advert['parameters'] ?? $advert['params'] ?? [];

        // Can be an array of objects [{key, value}] or flat map {key: value}
        if (is_array($params)) {
            $paramMap = [];
            // Handle [{key: 'make', value: 'Audi'}, ...] format
            if (isset($params[0]) && is_array($params[0])) {
                foreach ($params as $p) {
                    $key = $p['key'] ?? $p['name'] ?? $p['label'] ?? '';
                    $val = $p['value'] ?? $p['displayValue'] ?? $p['display_value'] ?? '';
                    if ($key && $val) {
                        $paramMap[strtolower($key)] = $val;
                    }
                }
            } else {
                // Handle {make: 'Audi', model: 'A4', ...} format
                $paramMap = array_change_key_case($params, CASE_LOWER);
            }

            $this->mapParams($paramMap, $data);
        }

        // Equipment/features
        $features = $advert['features'] ?? $advert['equipment'] ?? [];
        if (is_array($features) && !empty($features)) {
            $equipment = [];
            foreach ($features as $f) {
                if (is_string($f)) {
                    $equipment[] = $f;
                } elseif (is_array($f)) {
                    $equipment[] = $f['name'] ?? $f['label'] ?? $f['value'] ?? '';
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

        return $data;
    }

    private function mapParams(array $params, array &$data): void
    {
        // Direct mapping for common keys
        $directMap = [
            'make' => 'brand', 'marka' => 'brand', 'marka_pojazdu' => 'brand',
            'model' => 'model', 'model_pojazdu' => 'model',
            'year' => 'first_registration', 'rok' => 'first_registration', 'rok_produkcji' => 'first_registration',
            'mileage' => 'mileage', 'przebieg' => 'mileage',
            'engine_capacity' => 'engine_capacity', 'pojemnosc_skokowa' => 'engine_capacity', 'pojemność_skokowa' => 'engine_capacity',
            'engine_power' => 'power_hp', 'moc' => 'power_hp',
            'fuel_type' => 'fuel_type', 'rodzaj_paliwa' => 'fuel_type', 'paliwo' => 'fuel_type',
            'gearbox' => 'transmission', 'transmission' => 'transmission', 'skrzynia_biegow' => 'transmission', 'skrzynia_biegów' => 'transmission',
            'body_type' => 'body_type', 'typ_nadwozia' => 'body_type', 'nadwozie' => 'body_type',
            'color' => 'color', 'kolor' => 'color',
            'door_count' => 'doors', 'doors' => 'doors', 'liczba_drzwi' => 'doors',
            'nr_seats' => 'seats', 'seats' => 'seats', 'liczba_miejsc' => 'seats',
            'vin' => 'vin',
            'country_origin' => 'country_registration', 'kraj_pochodzenia' => 'country_registration',
            'nr_owner' => 'previous_owners', 'liczba_wlascicieli' => 'previous_owners',
            'drive' => 'drive_type', 'napęd' => 'drive_type', 'naped' => 'drive_type',
            'co2_emissions' => 'co2_emission', 'emisja_co2' => 'co2_emission',
        ];

        foreach ($directMap as $otomotoKey => $certiKey) {
            if (isset($params[$otomotoKey]) && !empty($params[$otomotoKey])) {
                $val = $params[$otomotoKey];
                $data[$certiKey] = $this->cleanValue($val, $certiKey);
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
            case 'engine_capacity':
            case 'doors':
            case 'seats':
            case 'previous_owners':
                return (int)preg_replace('/\D/', '', $value);
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
