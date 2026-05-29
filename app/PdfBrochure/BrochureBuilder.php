<?php

namespace App\PdfBrochure;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Turns a Car model into a fully-prepared BrochureData DTO.
 *
 * The builder is where every transformation between "raw DB row" and
 * "client-facing PDF content" happens — label mapping, free-text scrubbing,
 * image embedding. The view consumes BrochureData and only BrochureData; it
 * has no access to the model and no opportunity to leak a raw enum or an
 * unfetched image URL.
 */
final class BrochureBuilder
{
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

            mileage:           $car->mileage !== null ? (int) $car->mileage : null,
            firstRegistration: TextSanitizer::clean($car->first_registration),
            fuelType:          Labels::fuelType($car->fuel_type),
            transmission:      TextSanitizer::clean($car->transmission_detail) ?? Labels::transmission($car->transmission),
            powerHp:           $car->power_hp !== null ? (int) $car->power_hp : null,
            powerKw:           $car->power_kw !== null ? (int) $car->power_kw : null,
            engineCapacity:    $car->engine_capacity !== null ? (int) $car->engine_capacity : null,
            doors:             $car->doors !== null ? (int) $car->doors : null,
            seats:             $car->seats !== null ? (int) $car->seats : null,

            vin:        TextSanitizer::clean($car->vin),
            bodyType:   Labels::bodyType($car->body_type),
            color:      TextSanitizer::clean($car->color),
            colorCode:  TextSanitizer::clean($car->color_code),
            upholstery: TextSanitizer::clean($car->upholstery),
            driveType:  Labels::drive($car->drive_type),
            weight:     $car->weight !== null ? (int) $car->weight : null,
            numberOfKeys: $car->number_of_keys !== null ? (int) $car->number_of_keys : null,

            previousOwners:      $car->previous_owners !== null ? (int) $car->previous_owners : null,
            importedFrom:        TextSanitizer::clean($car->imported_from),
            countryRegistration: TextSanitizer::clean($car->country_registration),
            vehicleHistory:      TextSanitizer::clean($car->vehicle_history),

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
        ] + $this->embedder->stats());

        return [$data, $this->embedder];
    }

    /** ── Sub-builders ─────────────────────────────────────────────── */

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
        $out = [];
        foreach ($car->paint_measurements as $panel => $value) {
            $raw = is_array($value) ? ($value['value'] ?? $value[0] ?? 0) : $value;
            $val = (int) preg_replace('/[^0-9]/', '', (string) $raw);
            if ($val <= 0) continue;
            $label = is_array($value) && isset($value['area'])
                ? (TextSanitizer::clean($value['area']) ?? 'Panel')
                : (is_numeric($panel) ? ($panelNames[$panel] ?? 'Panel ' . ($panel + 1)) : (string) $panel);

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
                'severity'    => TextSanitizer::clean($d->severity),
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
            'exterior'   => 'Wygląd zewnętrzny',
            'interior'   => 'Wnętrze',
            'driving'    => 'Wspomaganie jazdy',
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
}
