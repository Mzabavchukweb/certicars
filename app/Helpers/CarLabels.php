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
}
