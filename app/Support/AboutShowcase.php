<?php

namespace App\Support;

use App\Models\Car;

/**
 * Przykładowe auto na stronie „O nas”: prezentacja 360° i podgląd CertiCheck.
 *
 * Wszystkie wartości pochodzą z ogłoszenia w bazie (te same pola, które
 * pokazuje strona auta i raport CertiCheck). Brak danych w danej części =
 * ta część się nie pokazuje, nic nie jest uzupełniane na sztywno.
 *
 * Wybór auta:
 *   1. slug z config('certicars.about_showcase_car'), jeśli auto jest aktywne,
 *   2. pierwsze aktywne auto z klatkami 360° z zewnątrz (najpierw z CertiCheck),
 *   3. pierwsze aktywne auto ze zdjęciami (najpierw z CertiCheck),
 *   4. null: sekcje 360° i podgląd CertiCheck się nie renderują.
 */
class AboutShowcase
{
    /** Tyle pomiarów lakieru mieści się w karcie (siatka 3×3). */
    private const PAINT_LIMIT = 9;

    /** Tyle śladów mieści się na mapie i w przełącznikach na telefonie. */
    private const DAMAGE_LIMIT = 6;

    private const PAINT_PANEL_NAMES = [
        0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
        4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
        8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
        11 => 'Zderzak P', 12 => 'Zderzak T', 13 => 'Próg lewy', 14 => 'Próg prawy',
    ];

    private const TIRE_POSITIONS = [
        'front_left'  => 'Przód lewy',
        'front_right' => 'Przód prawy',
        'rear_left'   => 'Tył lewy',
        'rear_right'  => 'Tył prawy',
    ];

    private const BODY_TYPE_TOP = [
        'sedan' => 'sedan', 'suv' => 'suv', 'coupé' => 'coupe', 'coupe' => 'coupe',
        'bus' => 'van', 'van' => 'van', 'kombi' => 'kombi', 'hatchback' => 'hatchback',
        'kabriolet' => 'sedan', 'cabriolet' => 'sedan', 'pickup' => 'suv',
    ];

    public static function build(): ?array
    {
        $car = self::pickCar();
        if (!$car) {
            return null;
        }

        $car->load('brand', 'images', 'damages.photos', 'tireSets.tires', 'pano360Image');

        $name = trim(($car->brand?->name ?? '') . ' ' . ($car->model ?? '')) ?: $car->title;
        // Krótki podpis z projektu („BMW 216d”) tylko dla auta wskazanego w configu.
        if ($car->slug === config('certicars.about_showcase_car') && config('certicars.about_showcase_label')) {
            $name = config('certicars.about_showcase_label');
        }

        $exterior = self::frames($car, 'exterior');
        $interior = self::frames($car, 'interior');
        if (!$interior && $car->pano360Image) {
            $interior = ['type' => 'pano', 'src' => route('panorama.stream', $car->pano360Image)];
        }

        // Zdjęcie główne z galerii (images zawiera też panoramy i zdjęcia śladów).
        $gallery = $car->images->where('type', 'gallery');
        $primary = $gallery->firstWhere('is_primary', true) ?? $gallery->first();
        $poster  = $primary && $primary->path ? $primary->url : null;

        $check = $car->has_certicheck ? [
            'paint'   => self::paint($car),
            'tires'   => self::tires($car),
            'damages' => self::damages($car),
        ] : null;
        if ($check && !$check['paint']['items'] && !$check['tires'] && !$check['damages']['items']) {
            $check = null;
        }

        return [
            'car'      => $car,
            'name'     => $name,
            'url'      => route('catalog.show', $car->slug),
            'checkUrl' => $car->has_certicheck ? route('catalog.certicheck', $car->slug) : route('catalog.show', $car->slug),
            'exterior' => $exterior,
            'interior' => $interior,
            'poster'   => $poster,
            'check'    => $check,
        ];
    }

    private static function pickCar(): ?Car
    {
        $slug = (string) config('certicars.about_showcase_car', '');
        if ($slug !== '') {
            $car = Car::active()->where('slug', $slug)->first();
            if ($car) {
                return $car;
            }
        }

        $withFrames = Car::active()
            ->where('exterior_frames_status', 'ready')
            ->where('exterior_frames_count', '>', 0)
            ->whereNotNull('exterior_frames_dir')
            ->orderByDesc('has_certicheck')
            ->latest()
            ->first();
        if ($withFrames) {
            return $withFrames;
        }

        return Car::active()
            ->whereHas('galleryImages')
            ->orderByDesc('has_certicheck')
            ->latest()
            ->first();
    }

    /** Klatki 360°: arkusze (szybciej) albo pojedyncze pliki. null = brak klatek. */
    private static function frames(Car $car, string $side): ?array
    {
        $has = $side === 'interior' ? $car->hasInteriorFrames() : $car->hasExteriorFrames();
        if (!$has) {
            return null;
        }
        $sprites = $car->frameSprites($side);
        $urls    = $sprites ? [] : ($side === 'interior' ? $car->interiorFrameUrls() : $car->exteriorFrameUrls());
        $count   = $sprites ? $sprites['n'] : count($urls);
        if ($count < 2) {
            return null;
        }
        return ['type' => 'frames', 'count' => $count, 'sprites' => $sprites, 'urls' => $urls];
    }

    private static function paint(Car $car): array
    {
        $items = [];
        foreach ((array) ($car->paint_measurements ?? []) as $panel => $value) {
            $val = is_array($value) ? ($value['value'] ?? $value[0] ?? '') : $value;
            $num = (int) preg_replace('/[^0-9]/', '', (string) $val);
            if ($num <= 0) {
                continue;
            }
            $label = is_array($value) && isset($value['area'])
                ? $value['area']
                : (is_numeric($panel) ? (self::PAINT_PANEL_NAMES[$panel] ?? 'Panel ' . ($panel + 1)) : $panel);
            $items[] = ['label' => (string) $label, 'value' => $num];
        }

        return [
            'items' => array_slice($items, 0, self::PAINT_LIMIT),
            'total' => count($items),
        ];
    }

    private static function tires(Car $car): ?array
    {
        if (!$car->tireSets || $car->tireSets->isEmpty()) {
            return null;
        }
        $set   = $car->tireSets->firstWhere('is_mounted', true) ?? $car->tireSets->first();
        $byPos = $set->tires->keyBy('position');

        $rows = [];
        $anyIssue = false;
        foreach (self::TIRE_POSITIONS as $key => $label) {
            $t = $byPos->get($key);
            if (!$t || $t->tread_depth === null || $t->tread_depth === '') {
                continue;
            }
            $status = self::tireStatus($t);
            $anyIssue = $anyIssue || $status !== 'ok';
            $rows[] = [
                'label'  => $label,
                'type'   => $set->tire_type,
                'depth'  => number_format((float) $t->tread_depth, 1, ',', ' '),
                'status' => $status,
            ];
        }
        if (!$rows) {
            return null;
        }

        return [
            'mounted'  => (bool) $set->is_mounted,
            'rows'     => $rows,
            // 16" -> 16″ (znak cala jak w projekcie), sama wartość bez zmian
            'rim'      => $set->rim ? preg_replace('/(\d)\s?"/u', '$1″', $set->rim) : null,
            'anyIssue' => $anyIssue,
        ];
    }

    /** Ta sama ocena co w karcie „Koła i opony” na stronie auta. */
    private static function tireStatus($t): string
    {
        $cond = is_array($t->condition) ? $t->condition : [];
        if (count($cond) === 0) {
            return 'ok';
        }
        $joined = mb_strtolower(implode(' ', $cond));
        foreach (['zużyt', 'uszkodz', 'wymian', 'pęknięt'] as $word) {
            if (str_contains($joined, $word)) {
                return 'bad';
            }
        }
        return 'warn';
    }

    private static function damages(Car $car): array
    {
        $btKey = strtolower((string) ($car->body_type ?? $car->category ?? 'sedan'));
        $map   = '/img/body-types-top/' . (self::BODY_TYPE_TOP[$btKey] ?? 'sedan') . '.png';

        $items = [];
        foreach ($car->damages->values() as $d) {
            $photo = $d->image_url;
            if (!$photo) {
                foreach ($d->photos ?? [] as $dp) {
                    if ($dp->path) { $photo = $dp->url; break; }
                }
            }
            if (!$photo) {
                continue; // punkt bez zdjęcia nie ma czego pokazać
            }
            // Te same kategorie co na stronie auta (catalog/show.blade.php).
            $sev = strtolower((string) ($d->severity ?? ''));
            $minor = in_array($sev, ['', 'low', 'minor', 'niski'], true);
            $items[] = [
                'x'     => (float) ($d->position_x ?? 50),
                'y'     => (float) ($d->position_y ?? 50),
                'photo' => $photo,
                'label' => $minor ? 'Drobne ślady eksploatacji' : 'Lekkie rysy i otarcia',
                'area'  => (string) $d->area,
            ];
            if (count($items) >= self::DAMAGE_LIMIT) {
                break;
            }
        }

        return ['map' => $map, 'items' => $items];
    }
}
