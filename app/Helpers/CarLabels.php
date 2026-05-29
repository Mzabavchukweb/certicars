<?php

namespace App\Helpers;

class CarLabels
{
    /**
     * Normalize an input to a lookup key: lowercase, trimmed, accents stripped.
     */
    private static function key(?string $v): string
    {
        if ($v === null) return '';
        $s = mb_strtolower(trim($v));
        // strip diacritics so "Niemcy" and "niemcy" both match
        $s = strtr($s, [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
            'ä'=>'a','ö'=>'o','ü'=>'u','ß'=>'ss',
            'é'=>'e','è'=>'e','ê'=>'e','à'=>'a','ç'=>'c',
        ]);
        return $s;
    }

    /** Fuel type → Polish display. Unknown values returned as-is (capitalized). */
    public static function fuelType(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'petrol' => 'Benzyna', 'gasoline' => 'Benzyna', 'benzin' => 'Benzyna', 'benzyna' => 'Benzyna',
            'diesel' => 'Diesel', 'olej napedowy' => 'Diesel', 'ropa' => 'Diesel',
            'electric' => 'Elektryczny', 'elektryczny' => 'Elektryczny', 'ev' => 'Elektryczny',
            'hybrid' => 'Hybryda', 'hybryda' => 'Hybryda',
            'plug-in hybrid' => 'Hybryda plug-in', 'phev' => 'Hybryda plug-in',
            'lpg' => 'LPG', 'cng' => 'CNG', 'ethanol' => 'Etanol',
        ];
        $k = self::key($value);
        return $map[$k] ?? ucfirst(mb_strtolower($value));
    }

    /** Transmission → Polish display. */
    public static function transmission(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'automatic' => 'Automatyczna', 'automatyczna' => 'Automatyczna', 'auto' => 'Automatyczna',
            'manual' => 'Manualna', 'manualna' => 'Manualna',
            'semi-automatic' => 'Półautomatyczna', 'polautomatyczna' => 'Półautomatyczna',
            'cvt' => 'CVT (bezstopniowa)', 'dct' => 'DCT (dwusprzęgłowa)', 'dsg' => 'DSG (dwusprzęgłowa)',
        ];
        $k = self::key($value);
        return $map[$k] ?? ucfirst(mb_strtolower($value));
    }

    /** Body type / category → Polish display. */
    public static function bodyType(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'sedan' => 'Sedan',
            'suv' => 'SUV',
            'coupe' => 'Coupé', 'coupé' => 'Coupé',
            'hatchback' => 'Hatchback',
            'kombi' => 'Kombi', 'wagon' => 'Kombi', 'estate' => 'Kombi',
            'van' => 'Bus', 'minivan' => 'Bus', 'bus' => 'Bus',
            'cabrio' => 'Kabriolet', 'convertible' => 'Kabriolet', 'cabriolet' => 'Kabriolet',
            'pickup' => 'Pickup', 'pick-up' => 'Pickup',
        ];
        $k = self::key($value);
        return $map[$k] ?? ucfirst(mb_strtolower($value));
    }

    /** Drive type → Polish display. */
    public static function drive(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'fwd' => 'Przedni', 'front' => 'Przedni', 'na przod' => 'Przedni',
            'rwd' => 'Tylny', 'rear' => 'Tylny', 'na tyl' => 'Tylny',
            'awd' => '4x4', '4wd' => '4x4', '4x4' => '4x4', 'allwheel' => '4x4', 'all-wheel' => '4x4',
        ];
        $k = self::key($value);
        return $map[$k] ?? ucfirst(mb_strtolower($value));
    }

    /** Yes/No-style booleans → "Tak" / "Nie". Returns null for unset/empty values. */
    public static function bool($value): ?string
    {
        if ($value === null || $value === '') return null;
        $k = self::key((string) $value);
        if (in_array($k, ['1', 'true', 'yes', 'tak', 'y', 'on', 't'], true)) return 'Tak';
        if (in_array($k, ['0', 'false', 'no', 'nie', 'n', 'off', 'f'], true)) return 'Nie';
        // pass through "Tak"/"Nie" already-translated strings
        if (in_array(mb_strtolower($value), ['tak', 'nie'], true)) return ucfirst(mb_strtolower($value));
        return null;
    }

    /**
     * Translate a known service/document state string like "complete" / "available" /
     * "missing" into Polish. Returns null when unrecognized so caller can fallback.
     */
    public static function status(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'complete' => 'Kompletna', 'kompletna' => 'Kompletna', 'full' => 'Kompletna',
            'incomplete' => 'Niekompletna', 'niekompletna' => 'Niekompletna', 'partial' => 'Częściowa',
            'available' => 'Dostępna', 'dostepna' => 'Dostępna', 'present' => 'Dostępna',
            'missing' => 'Brak', 'brak' => 'Brak', 'none' => 'Brak',
            'yes' => 'Tak', 'no' => 'Nie',
            'ok' => 'OK', 'good' => 'Dobry', 'sprawny' => 'Sprawny',
        ];
        $k = self::key($value);
        return $map[$k] ?? null;
    }

    /** Country name/code → Polish country name. */
    public static function country(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        $map = [
            'germany' => 'Niemcy', 'deutschland' => 'Niemcy', 'de' => 'Niemcy', 'niemcy' => 'Niemcy',
            'france' => 'Francja', 'fr' => 'Francja', 'francja' => 'Francja',
            'italy' => 'Włochy', 'italia' => 'Włochy', 'it' => 'Włochy', 'wlochy' => 'Włochy',
            'spain' => 'Hiszpania', 'es' => 'Hiszpania', 'espana' => 'Hiszpania', 'hiszpania' => 'Hiszpania',
            'netherlands' => 'Holandia', 'holland' => 'Holandia', 'nl' => 'Holandia', 'holandia' => 'Holandia',
            'belgium' => 'Belgia', 'be' => 'Belgia', 'belgia' => 'Belgia',
            'austria' => 'Austria', 'at' => 'Austria',
            'switzerland' => 'Szwajcaria', 'ch' => 'Szwajcaria', 'szwajcaria' => 'Szwajcaria',
            'denmark' => 'Dania', 'dk' => 'Dania', 'dania' => 'Dania',
            'sweden' => 'Szwecja', 'se' => 'Szwecja', 'szwecja' => 'Szwecja',
            'norway' => 'Norwegia', 'no' => 'Norwegia', 'norwegia' => 'Norwegia',
            'usa' => 'USA', 'us' => 'USA', 'united states' => 'USA',
            'japan' => 'Japonia', 'jp' => 'Japonia', 'japonia' => 'Japonia',
            'uk' => 'Wielka Brytania', 'gb' => 'Wielka Brytania', 'united kingdom' => 'Wielka Brytania',
            'czechia' => 'Czechy', 'czech' => 'Czechy', 'cz' => 'Czechy', 'czechy' => 'Czechy',
            'poland' => 'Polska', 'pl' => 'Polska', 'polska' => 'Polska',
        ];
        $k = self::key($value);
        return $map[$k] ?? $value;
    }

    /**
     * "Sprowadzony z {Country}" — returns null if no country info present.
     */
    public static function importedFromStatement($car): ?string
    {
        $country = $car->country_registration ?? $car->imported_from ?? null;
        if (!$country) return null;
        return 'Sprowadzony z ' . self::countryGenitive(self::country($country));
    }

    /** Naive nominative → genitive conversion for the country names we support. */
    private static function countryGenitive(?string $country): string
    {
        if (!$country) return '';
        $map = [
            'Niemcy' => 'Niemiec', 'Włochy' => 'Włoch', 'Czechy' => 'Czech',
            'Francja' => 'Francji', 'Hiszpania' => 'Hiszpanii', 'Holandia' => 'Holandii',
            'Belgia' => 'Belgii', 'Austria' => 'Austrii', 'Szwajcaria' => 'Szwajcarii',
            'Dania' => 'Danii', 'Szwecja' => 'Szwecji', 'Norwegia' => 'Norwegii',
            'Japonia' => 'Japonii', 'Polska' => 'Polski',
            'USA' => 'USA', 'Wielka Brytania' => 'Wielkiej Brytanii',
        ];
        return $map[$country] ?? $country;
    }

    /**
     * "Opłacona akcyza" — if the car's excise / taxation field indicates paid.
     * Returns null when not paid or unknown so the caller can hide the row.
     */
    public static function exciseStatement($car): ?string
    {
        $val = $car->taxation ?? null;
        if (!$val) return null;
        $k = self::key((string) $val);
        // common admin-side strings
        if (in_array($k, ['paid', 'oplacona', 'oplacono', 'opłacona', 'tak', 'true', '1', 'yes'], true)) {
            return 'Opłacona akcyza';
        }
        return null;
    }

    /**
     * Resolve a single technical_conditions entry into a normalised
     *   ['status' => 'ok'|'attention'|'bad', 'label' => string, 'note' => ?string]
     *
     * Accepts three input shapes for backward compatibility:
     *   1. Nested array — preferred:   ['status' => 'attention', 'note' => 'lekki stuk']
     *   2. Legacy free-text string:    'Sprawny' / 'Wymaga uwagi' / 'usterka'
     *   3. null / empty                — defaults to 'ok'
     *
     * Unknown / unrecognised strings default to 'ok' so we never falsely
     * mark something red. The keyword detection mirrors the keyword logic
     * the public + PDF views already use, just centralised here.
     */
    public static function techStatus($value): array
    {
        $allowed   = ['ok', 'attention', 'bad'];
        $statusKey = 'ok';
        $noteText  = null;

        if (is_array($value)) {
            // Explicit shape wins.
            $raw = isset($value['status']) ? strtolower(trim((string) $value['status'])) : '';
            if (in_array($raw, $allowed, true)) {
                $statusKey = $raw;
            } else {
                // Fall back to keyword-derive from any text the array carries
                // (e.g. legacy nested ['Sprawny'] from old admin saves).
                $statusKey = self::deriveTechStatus((string) ($value[0] ?? ''));
            }
            $noteRaw = $value['note'] ?? null;
            if (is_string($noteRaw) && trim($noteRaw) !== '') {
                $noteText = trim($noteRaw);
            }
        } elseif (is_string($value)) {
            $statusKey = self::deriveTechStatus($value);
        }

        $labelMap = [
            'ok'        => 'Bez zarzutu',
            'attention' => 'Wymaga uwagi',
            'bad'       => 'Nieprawidłowość',
        ];

        return [
            'status' => $statusKey,
            'label'  => $labelMap[$statusKey],
            'note'   => $noteText,
        ];
    }

    /**
     * Tire wheel position → Polish display. The DB column is a raw enum key
     * (front_left / rear_right / spare / top) that absolutely cannot appear
     * verbatim in a client-facing PDF. Unknown keys (admin typos) fall back
     * to a cleaned title-case string so e.g. "front_extra" reads as
     * "Front Extra" instead of leaking the raw column value.
     */
    public static function tirePosition(?string $key): string
    {
        $map = [
            'front_left'  => 'Przednia lewa',
            'front_right' => 'Przednia prawa',
            'rear_left'   => 'Tylna lewa',
            'rear_right'  => 'Tylna prawa',
            'spare'       => 'Zapasowe',
            'top'         => 'Górna',
        ];
        if (!is_string($key) || $key === '') return '—';
        return $map[$key] ?? mb_convert_case(str_replace('_', ' ', $key), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Tire condition (free-text array stored on CarTire::condition) → one of
     * the four canonical client labels. Raw admin input (slang like
     * "zajebiste", typos, English keys) never leaks through; unknowns fall
     * back to "Wymaga uwagi" rather than the raw string. Returns the label
     * plus a CSS class hint the views can use to colour the cell.
     */
    public static function tireCondition($cond): array
    {
        if (!is_array($cond) || count($cond) === 0) {
            return ['label' => 'Stan bardzo dobry', 'class' => 'cond-ok'];
        }
        $joined = mb_strtolower(implode(' ', array_filter($cond, 'is_string')));
        // Hard signals first — explicit replacement / damage tokens.
        if (preg_match('/wymian|do_wymiany|p[eę]kni|uszkodz|zniszcz|bad|broken|replace/u', $joined)) {
            return ['label' => 'Do wymiany', 'class' => 'cond-bad'];
        }
        if (str_contains($joined, 'zuży') || str_contains($joined, 'worn')) {
            return ['label' => 'Wymaga uwagi', 'class' => 'cond-warn'];
        }
        if (preg_match('/bardzo dobr|very good|excellent/u', $joined)) {
            return ['label' => 'Stan bardzo dobry', 'class' => 'cond-ok'];
        }
        if (preg_match('/\bok\b|dobry|good|sprawn/u', $joined)) {
            return ['label' => 'Dobry', 'class' => 'cond-ok'];
        }
        // Unknown text (admin typo / slang) → generic warning, never raw.
        return ['label' => 'Wymaga uwagi', 'class' => 'cond-warn'];
    }

    /**
     * Damage location enum (free-text on CarDamage::location, often using
     * the same front_left / rear_right family + body-panel names) → Polish
     * display. Same safety net as tirePosition: unknown values get cleaned
     * up rather than leaking raw.
     */
    public static function damageLocation(?string $key): string
    {
        if (!is_string($key) || $key === '') return '—';
        $map = [
            'front_left'   => 'Przód lewy',
            'front_right'  => 'Przód prawy',
            'rear_left'    => 'Tył lewy',
            'rear_right'   => 'Tył prawy',
            'front'        => 'Przód',
            'rear'         => 'Tył',
            'left'         => 'Lewy bok',
            'right'        => 'Prawy bok',
            'top'          => 'Dach',
            'roof'         => 'Dach',
            'hood'         => 'Maska',
            'trunk'        => 'Klapa bagażnika',
            'bumper_front' => 'Zderzak przedni',
            'bumper_rear'  => 'Zderzak tylny',
            'door_front_left'  => 'Drzwi przednie lewe',
            'door_front_right' => 'Drzwi przednie prawe',
            'door_rear_left'   => 'Drzwi tylne lewe',
            'door_rear_right'  => 'Drzwi tylne prawe',
            'interior'     => 'Wnętrze',
            'underbody'    => 'Podwozie',
        ];
        return $map[$key] ?? mb_convert_case(str_replace('_', ' ', $key), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Keyword-detect a status enum from a legacy free-text string.
     * Empty / unknown → 'ok' (so old data never falsely flags red).
     */
    private static function deriveTechStatus(string $text): string
    {
        $t = mb_strtolower(trim($text));
        if ($t === '') return 'ok';
        if (str_contains($t, 'usterk') || str_contains($t, 'niespraw') || str_contains($t, 'wymian')
            || str_contains($t, 'problem') || str_contains($t, 'nie działa') || str_contains($t, 'zle')
            || str_contains($t, 'źle') || str_contains($t, 'bad') || str_contains($t, 'broken')) {
            return 'bad';
        }
        if (str_contains($t, 'uwag') || str_contains($t, 'drobne') || str_contains($t, 'do sprawdzenia')
            || str_contains($t, 'lekki') || str_contains($t, 'fair') || str_contains($t, 'warning')
            || str_contains($t, 'attention')) {
            return 'attention';
        }
        // Empty / 'ok' / 'sprawny' / 'good' / 'pracuje prawidłowo' / 'bez zarzutu' / anything else → ok.
        return 'ok';
    }
}
