<?php

namespace App\Helpers;

/**
 * Catalog of equipment options shown on the public single-car page Wyposażenie
 * section.
 *
 * Each option has:
 *   - key     (stable identifier persisted in cars.highlighted_equipment)
 *   - label   (Polish display label)
 *   - icon    (Lucide icon name consumed by <x-icon>)
 *   - cat     (display category for the lower category cards)
 *
 * Admin selects up to 8 keys for cars.highlighted_equipment (the big top row).
 * The same key can repeat in multiple slots if admin chooses.
 *
 * If Lucide lacks an exact car-specific icon (e.g. steering wheel), the
 * closest available glyph is used — never an inline SVG.
 */
class EquipmentCatalog
{
    /** Display category keys → Polish display labels + Lucide head icon. */
    public const CATEGORIES = [
        'komfort'      => ['label' => 'Komfort',           'icon' => 'sofa'],
        'safety'       => ['label' => 'Bezpieczeństwo',    'icon' => 'shield'],
        'multimedia'   => ['label' => 'Multimedia',        'icon' => 'tv'],
        'exterior'     => ['label' => 'Światła i nadwozie','icon' => 'lightbulb'],
        'assist'       => ['label' => 'Systemy wspomagające', 'icon' => 'compass'],
        'other'        => ['label' => 'Inne',              'icon' => 'list-checks'],
    ];

    /**
     * Single source of truth for the option list. Key → label + Lucide icon +
     * display category. The admin dropdown for highlighted-equipment slots is
     * populated directly from this map.
     */
    public const OPTIONS = [
        // --- Komfort ---
        'air_auto_2zone'   => ['label' => 'Klimatyzacja automatyczna 2-strefowa', 'icon' => 'snowflake', 'cat' => 'komfort'],
        'air_auto'         => ['label' => 'Klimatyzacja automatyczna',            'icon' => 'snowflake', 'cat' => 'komfort'],
        'heated_seats'     => ['label' => 'Podgrzewane fotele przednie',          'icon' => 'flame',     'cat' => 'komfort'],
        'electric_windows' => ['label' => 'Elektryczne szyby przód i tył',        'icon' => 'square',    'cat' => 'komfort'],
        'electric_mirrors' => ['label' => 'Elektrycznie regulowane lusterka',     'icon' => 'square',    'cat' => 'komfort'],
        'heated_wheel'     => ['label' => 'Podgrzewana kierownica',               'icon' => 'flame',     'cat' => 'komfort'],
        'tinted_rear'      => ['label' => 'Przyciemniane szyby tylne',            'icon' => 'square-dashed','cat' => 'komfort'],
        'auto_skrzynia'    => ['label' => 'Automatyczna skrzynia',                'icon' => 'settings-2','cat' => 'komfort'],
        'seven_seats'      => ['label' => '7 miejsc',                             'icon' => 'users',     'cat' => 'komfort'],

        // --- Bezpieczeństwo ---
        'abs'              => ['label' => 'ABS',                                  'icon' => 'shield-check','cat' => 'safety'],
        'esp'              => ['label' => 'ESP',                                  'icon' => 'shield',    'cat' => 'safety'],
        'airbags'          => ['label' => 'Poduszki powietrzne',                  'icon' => 'shield-check','cat' => 'safety'],
        'isofix'           => ['label' => 'Isofix',                               'icon' => 'baby',      'cat' => 'safety'],
        'lane_assist'      => ['label' => 'Asystent pasa ruchu',                  'icon' => 'route',     'cat' => 'safety'],
        'blind_spot'       => ['label' => 'System monitorowania martwego pola',   'icon' => 'eye',       'cat' => 'safety'],

        // --- Multimedia ---
        'rlink2'           => ['label' => 'System multimedialny R-Link 2',        'icon' => 'tv',        'cat' => 'multimedia'],
        'nav'              => ['label' => 'Nawigacja satelitarna',                'icon' => 'map-pin',   'cat' => 'multimedia'],
        'carplay'          => ['label' => 'Apple CarPlay / Android Auto',         'icon' => 'smartphone','cat' => 'multimedia'],
        'bluetooth'        => ['label' => 'Bluetooth',                            'icon' => 'bluetooth', 'cat' => 'multimedia'],
        'dab'              => ['label' => 'Radio DAB',                            'icon' => 'radio',     'cat' => 'multimedia'],
        'usb_aux'          => ['label' => 'USB / AUX',                            'icon' => 'usb',       'cat' => 'multimedia'],

        // --- Światła i nadwozie ---
        'led_full'         => ['label' => 'Reflektory Full LED',                  'icon' => 'lightbulb', 'cat' => 'exterior'],
        'led_drl'          => ['label' => 'Światła do jazdy dziennej LED',        'icon' => 'sun',       'cat' => 'exterior'],
        'led_lights'       => ['label' => 'Światła LED',                          'icon' => 'lightbulb', 'cat' => 'exterior'],
        'fog_lights'       => ['label' => 'Światła przeciwmgielne',               'icon' => 'cloud-fog', 'cat' => 'exterior'],
        'alu_wheels_19'    => ['label' => 'Felgi aluminiowe 19"',                 'icon' => 'circle-dot','cat' => 'exterior'],
        'alu_wheels'       => ['label' => 'Felgi aluminiowe',                     'icon' => 'circle-dot','cat' => 'exterior'],
        'roof_rails'       => ['label' => 'Relingi dachowe',                      'icon' => 'minus',     'cat' => 'exterior'],
        'electric_tailgate'=> ['label' => 'Elektryczna klapa bagażnika',          'icon' => 'package-open','cat' => 'exterior'],
        'metallic_paint'   => ['label' => 'Lakier metalik',                       'icon' => 'sparkles',  'cat' => 'exterior'],

        // --- Systemy wspomagające ---
        'parking_front_rear'=> ['label' => 'Czujniki parkowania przód i tył',     'icon' => 'radio-tower','cat' => 'assist'],
        'parking_sensors'  => ['label' => 'Czujniki parkowania',                  'icon' => 'radio-tower','cat' => 'assist'],
        'rear_camera'      => ['label' => 'Kamera cofania',                       'icon' => 'video',     'cat' => 'assist'],
        'camera_360'       => ['label' => 'Kamera 360°',                          'icon' => 'rotate-3d', 'cat' => 'assist'],
        'cruise'           => ['label' => 'Tempomat',                             'icon' => 'gauge',     'cat' => 'assist'],
        'auto_hold'        => ['label' => 'Auto Hold',                            'icon' => 'hand',      'cat' => 'assist'],
        'sign_recognition' => ['label' => 'System rozpoznawania znaków drogowych','icon' => 'scan-line', 'cat' => 'assist'],
        'rain_sensor'      => ['label' => 'Czujnik deszczu',                      'icon' => 'cloud-rain','cat' => 'assist'],

        // --- Inne ---
        'keyless'          => ['label' => 'System bezkluczykowy',                 'icon' => 'key',       'cat' => 'other'],
        'folding_mirrors'  => ['label' => 'Składane lusterka elektrycznie',       'icon' => 'fold-vertical','cat' => 'other'],
        'cargo_blind'      => ['label' => 'Roleta bagażnika',                     'icon' => 'square',    'cat' => 'other'],
        'trip_computer'    => ['label' => 'Komputer pokładowy',                   'icon' => 'gauge',     'cat' => 'other'],
        'multi_wheel'      => ['label' => 'Wielofunkcyjna kierownica',            'icon' => 'circle',    'cat' => 'other'],
        'leather_wheel'    => ['label' => 'Skórzana kierownica',                  'icon' => 'circle',    'cat' => 'other'],
        'tow_hook'         => ['label' => 'Hak holowniczy',                       'icon' => 'link',      'cat' => 'other'],
    ];

    /** Returns the option row for a key, or null. */
    public static function option(?string $key): ?array
    {
        return self::OPTIONS[$key] ?? null;
    }

    /** Returns options grouped by display category — used by the admin dropdown to render optgroups. */
    public static function optionsGroupedByCategory(): array
    {
        $grouped = array_fill_keys(array_keys(self::CATEGORIES), []);
        foreach (self::OPTIONS as $key => $row) {
            $grouped[$row['cat']][$key] = $row;
        }
        return $grouped;
    }

    /**
     * Convert a car's persisted equipment data (flat list, list-of-categories,
     * or category map) into the 6 display categories used by the public page.
     *
     * Existing data shape on cars.equipment is `{safety:[...], comfort:[...],
     * exterior:[...], interior:[...], extra:[...]}` from the admin wizard form.
     * The mapping is intentionally lenient and falls back to keyword matching
     * against the option labels so legacy entries categorize correctly.
     */
    public static function groupEquipmentForDisplay($carEquipment): array
    {
        $out = array_fill_keys(array_keys(self::CATEGORIES), []);
        if (empty($carEquipment)) return $out;

        // Flatten possible nested shapes into [item => null] (preserve insertion order).
        $flat = [];
        if (is_array($carEquipment)) {
            $isAssoc = array_keys($carEquipment) !== range(0, count($carEquipment) - 1);
            if ($isAssoc) {
                // Legacy admin shape: {safety:[...], comfort:[...], ...}
                $legacyMap = [
                    'safety'   => 'safety',
                    'comfort'  => 'komfort',
                    'multimedia' => 'multimedia',
                    'exterior' => 'exterior',
                    'interior' => 'other',
                    'extra'    => 'other',
                    'inne'     => 'other',
                    'lights'   => 'exterior',
                    'assist'   => 'assist',
                ];
                foreach ($carEquipment as $cat => $items) {
                    $items = is_array($items) ? $items : [$items];
                    $displayCat = $legacyMap[strtolower((string) $cat)] ?? self::categorizeByLabel($cat);
                    foreach ($items as $item) {
                        if (!is_string($item) || trim($item) === '') continue;
                        $out[$displayCat][] = trim($item);
                    }
                }
            } else {
                // Flat list of strings.
                foreach ($carEquipment as $item) {
                    if (!is_string($item) || trim($item) === '') continue;
                    $out[self::categorizeByLabel($item)][] = trim($item);
                }
            }
        }

        // Drop empty categories so the frontend can skip rendering them.
        return array_filter($out, fn($items) => !empty($items));
    }

    /**
     * Best-effort categorization of a free-text equipment label into one of
     * the 6 display categories, by matching it against keyword hints. Used
     * when the legacy data doesn't pre-categorize.
     */
    private static function categorizeByLabel(string $label): string
    {
        $needle = mb_strtolower($label);
        $rules = [
            'safety'     => ['abs', 'esp', 'airbag', 'poduszk', 'isofix', 'asystent pasa', 'martwego', 'bezpiecz'],
            'multimedia' => ['radio', 'dab', 'bluetooth', 'usb', 'aux', 'nawigacj', 'carplay', 'android', 'multimedia', 'r-link'],
            'assist'     => ['kamera', 'czujnik', 'tempomat', 'parkowani', 'auto hold', 'rozpozna', 'deszczu', 'wspomag'],
            'exterior'   => ['lakier', 'felg', 'reling', 'led', 'reflektor', 'światł', 'klapa', 'mgieln', 'dachow', 'metalik'],
            'komfort'    => ['klimat', 'podgrz', 'fotel', 'lusterk', 'szyb', 'siedz', 'kierownic', 'skrzyni', 'automatyczn', '7 miejs'],
        ];
        foreach ($rules as $cat => $needles) {
            foreach ($needles as $n) {
                if (mb_strpos($needle, $n) !== false) return $cat;
            }
        }
        return 'other';
    }
}
