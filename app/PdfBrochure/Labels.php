<?php

namespace App\PdfBrochure;

/**
 * Single source of truth for translating internal DB enums + admin shorthand
 * into the Polish strings that appear in a client-facing CertiCheck PDF.
 *
 * Every public method is null-safe: pass null in, get a sensible Polish
 * fallback string out. Methods that classify a status (tireCondition,
 * techCondition) return an array with both the label and a CSS class hint so
 * the view can colour the row without re-classifying.
 *
 * Raw values that must NEVER reach the client (front_left, zajebiste, etc.)
 * are intercepted here. If a future enum value isn't mapped, the fallback
 * is a clean title-case version of the raw — never the underscore form.
 */
final class Labels
{
    /** ── Wheel positions ─────────────────────────────────────────── */
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
     * Tire condition → one of the four canonical client labels +
     * CSS class hint. Admin free-text array (including slang / typos) is
     * classified by keyword sniff so the raw value never leaks.
     *
     * @return array{label:string,class:string}
     */
    public static function tireCondition($cond): array
    {
        if (!is_array($cond) || count($cond) === 0) {
            return ['label' => 'Stan bardzo dobry', 'class' => 'cond-ok'];
        }
        $joined = mb_strtolower(implode(' ', array_filter($cond, 'is_string')));
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
        return ['label' => 'Wymaga uwagi', 'class' => 'cond-warn'];
    }

    /** ── Damage location panel names ─────────────────────────────── */
    public static function damageLocation(?string $key): string
    {
        if (!is_string($key) || $key === '') return '—';
        $map = [
            'front_left'        => 'Przód lewy',
            'front_right'       => 'Przód prawy',
            'rear_left'         => 'Tył lewy',
            'rear_right'        => 'Tył prawy',
            'front'             => 'Przód',
            'rear'              => 'Tył',
            'left'              => 'Lewy bok',
            'right'             => 'Prawy bok',
            'top'               => 'Dach',
            'roof'              => 'Dach',
            'hood'              => 'Maska',
            'trunk'             => 'Klapa bagażnika',
            'bumper_front'      => 'Zderzak przedni',
            'bumper_rear'       => 'Zderzak tylny',
            'door_front_left'   => 'Drzwi przednie lewe',
            'door_front_right'  => 'Drzwi przednie prawe',
            'door_rear_left'    => 'Drzwi tylne lewe',
            'door_rear_right'   => 'Drzwi tylne prawe',
            'interior'          => 'Wnętrze',
            'underbody'         => 'Podwozie',
        ];
        return $map[$key] ?? mb_convert_case(str_replace('_', ' ', $key), MB_CASE_TITLE, 'UTF-8');
    }

    /** ── Damage type ─────────────────────────────────────────────── */
    public static function damageType(?string $key): string
    {
        return match (strtolower((string) $key)) {
            'accident' => 'Wypadek',
            'repaired' => 'Naprawione',
            default    => 'Uszkodzenie',
        };
    }

    /**
     * Technical-condition status: caller may pass either a plain string
     * ('ok' / 'attention' / 'bad'), an array {status, note}, or a legacy
     * free-text label. Returns the canonical client label + CSS class.
     *
     * @return array{label:string,class:string,note:?string}
     */
    public static function techCondition($value): array
    {
        $note    = null;
        $status  = 'ok';

        if (is_array($value)) {
            $rawStatus = isset($value['status']) ? strtolower(trim((string) $value['status'])) : '';
            if (in_array($rawStatus, ['ok', 'attention', 'bad'], true)) {
                $status = $rawStatus;
            } else {
                $status = self::sniffTechStatus((string) ($value[0] ?? ''));
            }
            $rawNote = $value['note'] ?? null;
            if (is_string($rawNote) && trim($rawNote) !== '') {
                $note = trim($rawNote);
            }
        } elseif (is_string($value)) {
            $status = self::sniffTechStatus($value);
        }

        $labels = [
            'ok'        => 'Bez zarzutu',
            'attention' => 'Wymaga uwagi',
            'bad'       => 'Wymaga naprawy',
        ];
        $classes = [
            'ok'        => 'cond-ok',
            'attention' => 'cond-warn',
            'bad'       => 'cond-bad',
        ];

        return [
            'label' => $labels[$status],
            'class' => $classes[$status],
            'note'  => $note,
        ];
    }

    private static function sniffTechStatus(string $text): string
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
        return 'ok';
    }

    /** ── Lookups copied wholesale from the older CarLabels helper ──
     *  Kept here so the brochure namespace is fully self-contained and
     *  the unrelated CarLabels usage elsewhere in the public site is not
     *  affected by changes the PDF needs. ──────────────────────────── */
    public static function fuelType(?string $value): ?string
    {
        if ($value === null || trim($value) === '') return null;
        $map = [
            'petrol' => 'Benzyna', 'gasoline' => 'Benzyna', 'benzin' => 'Benzyna', 'benzyna' => 'Benzyna',
            'diesel' => 'Diesel', 'olej napedowy' => 'Diesel', 'ropa' => 'Diesel',
            'electric' => 'Elektryczny', 'elektryczny' => 'Elektryczny', 'ev' => 'Elektryczny',
            'hybrid' => 'Hybryda', 'hybryda' => 'Hybryda',
            'plug-in hybrid' => 'Hybryda plug-in', 'phev' => 'Hybryda plug-in',
            'lpg' => 'LPG', 'cng' => 'CNG', 'ethanol' => 'Etanol',
        ];
        return $map[self::key($value)] ?? ucfirst(mb_strtolower($value));
    }

    public static function transmission(?string $value): ?string
    {
        if ($value === null || trim($value) === '') return null;
        $map = [
            'automatic' => 'Automatyczna', 'automatyczna' => 'Automatyczna', 'auto' => 'Automatyczna',
            'manual' => 'Manualna', 'manualna' => 'Manualna',
            'semi-automatic' => 'Półautomatyczna', 'polautomatyczna' => 'Półautomatyczna',
            'cvt' => 'CVT (bezstopniowa)', 'dct' => 'DCT (dwusprzęgłowa)', 'dsg' => 'DSG (dwusprzęgłowa)',
        ];
        return $map[self::key($value)] ?? ucfirst(mb_strtolower($value));
    }

    public static function bodyType(?string $value): ?string
    {
        if ($value === null || trim($value) === '') return null;
        $map = [
            'sedan'    => 'Sedan', 'suv' => 'SUV',
            'coupe'    => 'Coupé', 'coupé' => 'Coupé',
            'hatchback' => 'Hatchback',
            'kombi'    => 'Kombi', 'wagon' => 'Kombi', 'estate' => 'Kombi',
            'van'      => 'Bus', 'minivan' => 'Bus', 'bus' => 'Bus',
            'cabrio'   => 'Kabriolet', 'convertible' => 'Kabriolet', 'cabriolet' => 'Kabriolet',
            'pickup'   => 'Pickup', 'pick-up' => 'Pickup',
        ];
        return $map[self::key($value)] ?? ucfirst(mb_strtolower($value));
    }

    public static function drive(?string $value): ?string
    {
        if ($value === null || trim($value) === '') return null;
        $map = [
            'fwd' => 'Przedni', 'front' => 'Przedni', 'na przod' => 'Przedni',
            'rwd' => 'Tylny',   'rear'  => 'Tylny',   'na tyl'   => 'Tylny',
            'awd' => '4x4',     '4wd'   => '4x4',     '4x4'      => '4x4',
            'allwheel' => '4x4', 'all-wheel' => '4x4',
        ];
        return $map[self::key($value)] ?? ucfirst(mb_strtolower($value));
    }

    private static function key(string $v): string
    {
        return strtr(mb_strtolower(trim($v)), [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
            'ä'=>'a','ö'=>'o','ü'=>'u','ß'=>'ss',
            'é'=>'e','è'=>'e','ê'=>'e','à'=>'a','ç'=>'c',
        ]);
    }
}
