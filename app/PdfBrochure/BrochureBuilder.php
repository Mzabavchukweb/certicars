<?php

namespace App\PdfBrochure;

use App\Helpers\CarLabels;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Support\Facades\Log;

/**
 * Turns a Car model into a fully-prepared BrochureData DTO.
 *
 * The builder is where every transformation between "raw DB row" and
 * "client-facing PDF content" happens — label mapping, free-text scrubbing,
 * image embedding. The view consumes BrochureData and only BrochureData; it
 * has no access to the model and no opportunity to leak a raw enum or an
 * unfetched image URL.
 *
 * Every section assembly (history, documents, formal, service, fuel) goes
 * through the same `kv()` helper: produces `['label','value']` rows only
 * when the value is non-empty. The view loops the array and renders rows;
 * no extra "is this field filled?" branching in the template.
 */
final class BrochureBuilder
{
    /** Public contact details rendered on every page header. */
    public const CONTACT_PHONE   = '+48 515 440 623';
    public const CONTACT_EMAIL   = 'kontakt@certicars.pl';
    public const CONTACT_WEBSITE = 'certicars.pl';

    public function __construct(
        private readonly ImageEmbedder $embedder,
    ) {}

    /**
     * Build returns a pair: [BrochureData, ImageEmbedder]. The embedder is
     * kept addressable so the controller can pull stats() and manifest()
     * for logging / the diagnostic endpoint.
     *
     * @return array{0:BrochureData,1:ImageEmbedder}
     */
    public function build(Car $car, string $reportId): array
    {
        // Eager-load every relation the brochure touches so we don't fire
        // N+1 queries inside the section loops.
        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires');

        $data = new BrochureData(
            reportId:    $reportId,
            generatedAt: now()->format('d.m.Y'),
            identifier:  (string) ($car->identifier ?? ''),
            title:       (string) ($car->title ?? ''),
            brand:       $car->brand?->name,
            model:       TextSanitizer::clean($car->model),
            formattedPrice: $car->formatted_price ?? null,
            price:       $car->price !== null ? (int) $car->price : null,

            contactPhone:   self::CONTACT_PHONE,
            contactEmail:   self::CONTACT_EMAIL,
            contactWebsite: self::CONTACT_WEBSITE,

            mileage:           $car->mileage !== null ? (int) $car->mileage : null,
            firstRegistration: TextSanitizer::clean($car->first_registration),
            receptionDate:     $car->reception_date ? $car->reception_date->format('d.m.Y') : null,
            fuelType:          Labels::fuelType($car->fuel_type),
            transmission:      TextSanitizer::clean($car->transmission_detail) ?? Labels::transmission($car->transmission),
            powerHp:           $car->power_hp !== null ? (int) $car->power_hp : null,
            powerKw:           $car->power_kw !== null ? (int) $car->power_kw : null,
            engineCapacity:    $car->engine_capacity !== null ? (int) $car->engine_capacity : null,
            doors:             $car->doors !== null ? (int) $car->doors : null,
            seats:             $car->seats !== null ? (int) $car->seats : null,

            vehicleData:    $this->buildVehicleData($car),
            historyItems:   $this->buildHistoryItems($car),
            documentItems:  $this->buildDocumentItems($car),
            formalItems:    $this->buildFormalItems($car),
            serviceItems:   $this->buildServiceItems($car),
            fuelItems:      $this->buildFuelItems($car),

            vehicleHistoryNote: TextSanitizer::clean($car->vehicle_history),

            technicalConditions: $this->buildTechnical($car),
            paintMeasurements:   $this->buildPaint($car),
            tireSets:            $this->buildTireSets($car),
            damages:             $this->buildDamages($car),
            equipment:           $this->buildEquipment($car),

            heroImage:     $this->embedHero($car),
            galleryImages: $this->embedCollection($car->galleryImages ?? collect(), 'gallery'),
            damageImages:  $this->embedCollection($car->damageImages ?? collect(), 'damage_aggregate'),

            engineVideoUrl:  $this->safeUrl($car->engine_video_url),
            exteriorPanoUrl: $car->exteriorPano360Image ? url('/samochody/' . $car->slug) : null,
            interiorPanoUrl: $car->pano360Image ? url('/samochody/' . $car->slug) : null,
        );

        Log::info('pdf_brochure.built', [
            'report_id'         => $reportId,
            'car_id'            => $car->id,
            'gallery_count'     => count($data->galleryImages),
            'damage_count'      => count($data->damageImages),
            'damage_card_count' => count($data->damages),
            'tire_set_count'    => count($data->tireSets),
            'has_hero'          => $data->heroImage !== null,
            'vehicle_data_rows' => count($data->vehicleData),
            'history_rows'      => count($data->historyItems),
            'document_rows'     => count($data->documentItems),
            'formal_rows'       => count($data->formalItems),
            'service_rows'      => count($data->serviceItems),
            'fuel_rows'         => count($data->fuelItems),
        ] + $this->embedder->stats());

        return [$data, $this->embedder];
    }

    /** ── Section builders (every row sanitized + null-safe) ─────────── */

    /**
     * Main Dane pojazdu table on page 1. Order chosen to match a
     * professional inspection report: identification → engine → body →
     * appearance. Every filled field appears; empty fields drop out.
     */
    private function buildVehicleData(Car $car): array
    {
        $rows = [];

        $this->kv($rows, 'Marka', $car->brand?->name);
        $this->kv($rows, 'Model', TextSanitizer::clean($car->model));
        $this->kv($rows, 'Wersja', TextSanitizer::clean($car->transmission_detail));
        $this->kv($rows, 'Wersja wyposażenia', TextSanitizer::clean($car->equipment_version));
        // VIN is a 17-char structured identifier; profanity filtering would
        // false-positive on legitimate manufacturer codes (Audi's "WAUZZZ"
        // prefix matches the "zzz" placeholder stem). Validate shape only.
        $this->kv($rows, 'VIN', $this->cleanVin($car->vin));
        $this->kv($rows, 'Typ nadwozia', Labels::bodyType($car->body_type));
        $this->kv($rows, 'Rok produkcji', TextSanitizer::clean($car->first_registration));
        $this->kv(
            $rows,
            'Pierwsza rejestracja',
            $car->reception_date ? $car->reception_date->format('d.m.Y') : null,
        );
        $this->kv(
            $rows,
            'Przebieg',
            $car->mileage !== null ? number_format((int) $car->mileage, 0, '', ' ') . ' km' : null,
        );
        $this->kv($rows, 'Paliwo', Labels::fuelType($car->fuel_type));
        $this->kv(
            $rows,
            'Pojemność silnika',
            $car->engine_capacity !== null ? number_format((int) $car->engine_capacity, 0, '', ' ') . ' cm³' : null,
        );
        $this->kv(
            $rows,
            'Moc',
            $car->power_hp !== null
                ? (int) $car->power_hp . ' KM' . ($car->power_kw !== null ? ' / ' . (int) $car->power_kw . ' kW' : '')
                : null,
        );
        $this->kv($rows, 'Skrzynia biegów', Labels::transmission($car->transmission));
        $this->kv($rows, 'Napęd', Labels::drive($car->drive_type));
        $this->kv($rows, 'Liczba drzwi', $car->doors !== null ? (string) (int) $car->doors : null);
        $this->kv($rows, 'Liczba miejsc', $car->seats !== null ? (string) (int) $car->seats : null);

        $color = TextSanitizer::clean($car->color);
        $code  = TextSanitizer::clean($car->color_code);
        $this->kv($rows, 'Kolor nadwozia', $color !== null ? ($color . ($code ? ' (' . $code . ')' : '')) : null);
        $this->kv($rows, 'Kolor wnętrza / tapicerka', TextSanitizer::clean($car->upholstery));
        $this->kv($rows, 'Liczba kluczyków', $car->number_of_keys !== null ? (string) (int) $car->number_of_keys : null);
        $this->kv($rows, 'Kraj pochodzenia', CarLabels::country($car->country_registration ?? $car->imported_from));
        $this->kv(
            $rows,
            'Masa własna',
            $car->weight !== null ? number_format((int) $car->weight, 0, '', ' ') . ' kg' : null,
        );

        return $rows;
    }

    /**
     * History section. "Importowany" produces a clean Tak / specific country
     * value instead of leaking the raw imported_from string twice (we already
     * show country of origin in vehicleData).
     */
    private function buildHistoryItems(Car $car): array
    {
        $rows = [];

        $country = CarLabels::country($car->country_registration);
        $this->kv($rows, 'Pochodzenie', $country);

        $importedFrom = TextSanitizer::clean($car->imported_from);
        $importValue = null;
        if ($importedFrom !== null && $importedFrom !== $car->country_registration) {
            $importValue = CarLabels::country($importedFrom);
        } elseif ($car->is_imported || $importedFrom !== null) {
            $importValue = 'Tak';
        }
        $this->kv($rows, 'Importowany', $importValue);

        if ($car->previous_owners !== null) {
            $value = (int) $car->previous_owners === 0 ? 'Pierwszy właściciel' : (string) (int) $car->previous_owners;
            $this->kv($rows, 'Liczba właścicieli', $value);
        }

        return $rows;
    }

    /**
     * Documents section. Faktura defaults to "VAT-marża" — that's the
     * universal CertiCars sale form, same as the public single-car page.
     */
    private function buildDocumentItems(Car $car): array
    {
        $rows = [];

        $this->kv($rows, 'Faktura', 'VAT-marża');

        $reg = CarLabels::bool($car->registration_cert) ?? CarLabels::status($car->registration_cert);
        $this->kv($rows, 'Dowód rejestracyjny', $reg);

        $book = CarLabels::status($car->service_book_status)
            ?? CarLabels::bool($car->service_book)
            ?? TextSanitizer::clean($car->service_book_status);
        $this->kv($rows, 'Książka serwisowa', $book);

        $manual = CarLabels::bool($car->owners_manual) ?? CarLabels::status($car->owners_manual);
        $this->kv($rows, 'Instrukcja obsługi', $manual);

        $coc = CarLabels::bool($car->coc_documents);
        $this->kv($rows, 'Komplet dokumentów', $coc);

        $folder = CarLabels::bool($car->vehicle_folder);
        $this->kv($rows, 'Teczka pojazdu', $folder);

        $hu = CarLabels::bool($car->hu_au_report);
        $this->kv($rows, 'Raport HU/AU', $hu);

        return $rows;
    }

    /**
     * Formalities section. PCC / registration cost / transport are
     * CertiCars-wide policy lines — they appear on every public car page
     * and are shown unconditionally so the buyer has a complete summary.
     */
    private function buildFormalItems(Car $car): array
    {
        $rows = [];

        $excise = match (strtolower((string) $car->taxation)) {
            'paid', 'oplacona', 'opłacona' => 'Opłacona',
            'unpaid', 'nieoplacona', 'nieopłacona' => 'Nieopłacona',
            'na', 'nie_dotyczy', 'nie dotyczy' => 'Nie dotyczy',
            default => TextSanitizer::clean($car->taxation),
        };
        $this->kv($rows, 'Akcyza', $excise ?: 'Opłacona');

        $inspection = TextSanitizer::clean($car->next_inspection);
        $this->kv($rows, 'Przegląd techniczny', $inspection ?: 'Wykonany');

        $this->kv($rows, 'Przygotowany do rejestracji', 'Tak');
        $this->kv($rows, 'PCC 2%', 'Kupujący zwolniony');
        $this->kv($rows, 'Koszt rejestracji', 'Po stronie kupującego');
        $this->kv($rows, 'Możliwość transportu', 'Dostępna po wcześniejszym ustaleniu');

        return $rows;
    }

    /** Service block — facts admin can fill freely; nothing invented. */
    private function buildServiceItems(Car $car): array
    {
        $rows = [];

        $this->kv($rows, 'Serwis ASO', CarLabels::bool($car->aso_serviced));
        $this->kv($rows, 'Dokumentacja serwisowa', CarLabels::bool($car->service_documentation));
        $this->kv($rows, 'Historia serwisowa', TextSanitizer::clean($car->service_history));

        $last = TextSanitizer::clean($car->last_service);
        if ($last !== null) {
            $value = $last;
            if ($car->last_service_mileage !== null) {
                $value .= ' · ' . number_format((float) $car->last_service_mileage, 0, '', ' ') . ' km';
            }
            $this->kv($rows, 'Ostatni przegląd', $value);
        }

        return $rows;
    }

    /** Fuel & emissions. Strips embedded "l/100 km" admin may have typed. */
    private function buildFuelItems(Car $car): array
    {
        $rows = [];

        // Single source of truth: CarLabels formatters used by both PDF and
        // the public detail page. Keeps PDF ↔ detail strings byte-identical.
        $fc = CarLabels::fuelConsumption($car->fuel_consumption);
        if ($fc !== null) $this->kv($rows, 'Średnie zużycie', $fc);

        $co2 = CarLabels::co2Emission($car->co2_emission);
        if ($co2 !== null) $this->kv($rows, 'Emisja CO₂', $co2);

        $emission = CarLabels::emissionClass($car->emission_class);
        if ($emission !== null) $this->kv($rows, 'Norma emisji', $emission);

        return $rows;
    }

    /**
     * Push a row only when value is non-empty after sanitization. Centralised
     * here so the section helpers stay declarative.
     */
    private function kv(array &$rows, string $label, ?string $value): void
    {
        if ($value === null) return;
        $value = trim($value);
        if ($value === '' || $value === '—') return;
        $rows[] = ['label' => $label, 'value' => $value];
    }

    /** ── Per-section deep builders ──────────────────────────────────── */

    private function buildTechnical(Car $car): array
    {
        if (!$car->technical_conditions || !is_array($car->technical_conditions)) return [];

        $compLabels = [
            'engine'           => 'Silnik',
            'transmission'     => 'Skrzynia biegów',
            'suspension'       => 'Zawieszenie',
            'electronics'      => 'Elektronika',
            'body'             => 'Nadwozie',
            'brakes'           => 'Hamulce',
            'braking'          => 'Układ hamulcowy',
            'steering'         => 'Układ kierowniczy',
            'exhaust'          => 'Układ wydechowy',
            'ac'               => 'Klimatyzacja',
            'air_conditioning' => 'Klimatyzacja',
            'tires'            => 'Opony',
            'lights'           => 'Oświetlenie',
            'interior'         => 'Wnętrze',
            'underbody'        => 'Podwozie',
        ];
        $out = [];
        foreach ($car->technical_conditions as $key => $value) {
            $resolved  = Labels::techCondition($value);
            $cleanNote = TextSanitizer::clean($resolved['note']);
            $out[] = [
                'key'    => (string) $key,
                'label'  => $compLabels[strtolower((string) $key)] ?? ucfirst((string) $key),
                'status' => $resolved['label'],
                'class'  => $resolved['class'],
                'note'   => $cleanNote,
            ];
        }
        return $out;
    }

    private function buildPaint(Car $car): array
    {
        if (!$car->paint_measurements || !is_array($car->paint_measurements)) return [];

        $panelNames = [
            0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
            4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
            8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
            11 => 'Zderzak przód', 12 => 'Zderzak tył', 13 => 'Próg lewy', 14 => 'Próg prawy',
        ];
        // Keyed paint locations (admin may save by name instead of index)
        $keyMap = [
            'hood' => 'Maska', 'maska' => 'Maska',
            'roof' => 'Dach', 'dach' => 'Dach',
            'trunk' => 'Klapa bagażnika', 'tailgate' => 'Klapa bagażnika', 'klapa' => 'Klapa bagażnika',
            'front_bumper' => 'Zderzak przedni', 'rear_bumper' => 'Zderzak tylny',
            'front_left_door' => 'Drzwi przednie lewe', 'front_right_door' => 'Drzwi przednie prawe',
            'rear_left_door' => 'Drzwi tylne lewe', 'rear_right_door' => 'Drzwi tylne prawe',
            'left_sill' => 'Próg lewy', 'right_sill' => 'Próg prawy',
            'left_fender' => 'Błotnik lewy', 'right_fender' => 'Błotnik prawy',
            'front_left_fender' => 'Błotnik przedni lewy', 'front_right_fender' => 'Błotnik przedni prawy',
            'rear_left_fender' => 'Błotnik tylny lewy', 'rear_right_fender' => 'Błotnik tylny prawy',
        ];
        $out = [];
        foreach ($car->paint_measurements as $panel => $value) {
            $raw = is_array($value) ? ($value['value'] ?? $value[0] ?? 0) : $value;
            $val = (int) preg_replace('/[^0-9]/', '', (string) $raw);
            if ($val <= 0) continue;

            if (is_array($value) && isset($value['area'])) {
                $label = TextSanitizer::clean($value['area']) ?? 'Panel';
            } elseif (is_numeric($panel)) {
                $label = $panelNames[$panel] ?? 'Panel ' . ($panel + 1);
            } else {
                $key = strtolower((string) $panel);
                $label = $keyMap[$key] ?? mb_convert_case(str_replace('_', ' ', $key), MB_CASE_TITLE, 'UTF-8');
            }

            [$class, $verdict] = match (true) {
                $val > 200 => ['paint-danger', 'Naprawa'],
                $val > 160 => ['paint-warn',   'Uwaga'],
                default    => ['paint-ok',     'OK'],
            };
            $out[] = ['label' => $label, 'value' => $val, 'class' => $class, 'verdict' => $verdict];
        }
        return $out;
    }

    private function buildTireSets(Car $car): array
    {
        if (!$car->tireSets || $car->tireSets->isEmpty()) return [];

        $out = [];
        foreach ($car->tireSets as $i => $set) {
            $titleParts = ['Komplet ' . ($set->set_number ?? ($i + 1))];
            $tireType   = TextSanitizer::clean($set->tire_type);
            $rim        = TextSanitizer::clean($set->rim);
            if ($tireType) $titleParts[] = $tireType;
            if ($rim)      $titleParts[] = $rim;
            $title = implode(' · ', $titleParts);
            if ($set->is_mounted) $title .= ' (zamontowane)';

            $tires = [];
            foreach ($set->tires as $tire) {
                $cond = Labels::tireCondition($tire->condition);
                $tires[] = [
                    'position' => Labels::tirePosition($tire->position),
                    'treadMm'  => $tire->tread_depth !== null
                        ? number_format((float) $tire->tread_depth, 1, ',', ' ') . ' mm'
                        : null,
                    'label'    => $cond['label'],
                    'class'    => $cond['class'],
                ];
            }
            $out[] = ['title' => $title, 'tires' => $tires];
        }
        return $out;
    }

    private function buildDamages(Car $car): array
    {
        if (!$car->damages || $car->damages->isEmpty()) return [];

        $out = [];
        foreach ($car->damages as $d) {
            $rawArea = (string) ($d->area ?? '');
            // If the value looks like an enum key (snake_case or starts
            // lower-case) put it through the location map; otherwise treat
            // as already-localised free text and just sanitize.
            $areaCandidate = (str_contains($rawArea, '_') || ($rawArea !== '' && ctype_lower($rawArea[0] ?? 'X')))
                ? Labels::damageLocation($rawArea)
                : $rawArea;
            $area = TextSanitizer::clean($areaCandidate) ?? '—';

            $photos = [];
            if (!empty($d->image_path)) {
                $img = $this->embedder->embed($d->image_path, 'damage_main');
                if ($img) $photos[] = $img;
            }
            if ($d->photos) {
                foreach ($d->photos as $dp) {
                    $img = $this->embedder->embed($dp->path, 'damage_photo');
                    if ($img && !$this->alreadyInList($photos, $img)) $photos[] = $img;
                }
            }

            $out[] = [
                'area'        => $area,
                'type'        => Labels::damageType($d->type),
                'severity'    => Labels::damageSeverity($d->severity),
                'tags'        => TextSanitizer::cleanArray(is_array($d->tags) ? $d->tags : null),
                'description' => TextSanitizer::clean($d->description),
                'photos'      => $photos,
            ];
        }
        return $out;
    }

    private function buildEquipment(Car $car): array
    {
        if (!$car->equipment || !is_array($car->equipment)) return [];

        $catLabels = [
            'safety'     => 'Bezpieczeństwo',
            'comfort'    => 'Komfort',
            'multimedia' => 'Multimedia',
            'exterior'   => 'Światła i nadwozie',
            'interior'   => 'Wnętrze',
            'driving'    => 'Wspomaganie jazdy',
            'extra'      => 'Inne',
            'other'      => 'Inne',
        ];
        $out = [];
        foreach ($car->equipment as $cat => $items) {
            if (!is_array($items)) continue;
            $cleanItems = TextSanitizer::cleanArray($items);
            if (count($cleanItems) === 0) continue;
            $out[] = [
                'title' => $catLabels[strtolower((string) $cat)] ?? ucfirst((string) $cat),
                'items' => $cleanItems,
            ];
        }
        return $out;
    }

    /** ── Image embedding helpers ──────────────────────────────────── */

    private function embedHero(Car $car): ?EmbeddedImage
    {
        $primary = $car->primaryImage;
        if (!$primary) return null;
        return $this->embedder->embed($primary->path, 'hero');
    }

    /**
     * @param iterable<CarImage> $collection
     * @return array<int,EmbeddedImage>
     */
    private function embedCollection(iterable $collection, string $context): array
    {
        $seen = [];
        $out  = [];
        foreach ($collection as $img) {
            $embedded = $this->embedder->embed($img->path, $context);
            if ($embedded === null) continue;
            // Dedup by source path inside a single section.
            if (isset($seen[$embedded->sourcePath])) continue;
            $seen[$embedded->sourcePath] = true;
            $out[] = $embedded;
        }
        return $out;
    }

    /** @param array<int,EmbeddedImage> $existing */
    private function alreadyInList(array $existing, EmbeddedImage $img): bool
    {
        foreach ($existing as $e) {
            if ($e->sourcePath === $img->sourcePath) return true;
        }
        return false;
    }

    private function safeUrl(?string $url): ?string
    {
        $url = TextSanitizer::clean($url);
        if ($url === null) return null;
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Normalise a VIN without running it through the profanity filter.
     * A VIN is 17 chars of [A-Z0-9] excluding I, O, Q. Anything that
     * doesn't look like one drops to null so we never echo admin scratch.
     */
    private function cleanVin(?string $vin): ?string
    {
        if (!is_string($vin)) return null;
        $vin = strtoupper(trim($vin));
        if ($vin === '') return null;
        // Accept any 11–17 char alphanumeric string. Some legacy VINs are
        // shorter; admin pre-2000-imports occasionally save 11–16 chars.
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{11,17}$/', $vin)) return null;
        return $vin;
    }
}
