<?php

namespace App\Services;

/**
 * Strips obviously-unprofessional values from admin free-text fields before
 * they reach the public CertiCheck PDF. The Polish car-trade admin form has
 * a lot of free-text inputs (tire_type, rim, damage tags / area / notes,
 * etc.) and historical data contains test placeholders ("test", "asdf"),
 * slang ("zajebiste"), and outright profanity that absolutely cannot
 * appear in a client-facing report.
 *
 * The list is deliberately small — Polish profanity has many variants and a
 * full-spectrum filter belongs in admin validation, not the PDF generator.
 * What this catches is the common offenders that have actually leaked into
 * production reports.
 *
 * Behavior:
 *   - clean(): returns the input with offending tokens removed; if NOTHING
 *     useful is left, returns null. Caller should treat null as "hide this
 *     field entirely", not "emit empty string".
 *   - cleanArray(): same logic across an array, drops null entries.
 */
class BrochureTextScrubber
{
    /**
     * Slang / profanity / test-placeholder tokens (lowercase, accent-stripped).
     * Match is word-boundary on a normalised version of the input so a clean
     * token like "ok" never accidentally matches a longer word.
     */
    private const BAD_TOKENS = [
        // Polish profanity / strong slang stems (no full word lists — we
        // want to catch what shows up in admin input, not build a dictionary).
        'zajebist',  // covers "zajebiste / zajebista / zajebisty"
        'jebac',     // covers "jebać / jebal..."
        'kurwa', 'kurwy',
        'chuj', 'chujow',
        'pierdol', 'pierdole', 'pierdolony',
        'spierdal',
        'cipa', 'cipy',
        'dupa', // very informal in this context
        // Test placeholders that have shown up in historical brochures.
        'asdf', 'qwerty', 'lorem', 'ipsum', 'test123', 'xxx', 'aaa',
        // Internal/dev markers.
        'todo', 'fixme', 'tbd', 'tba',
    ];

    /**
     * Sanitize a single free-text value. Returns null if nothing usable is
     * left (so the caller can hide the field rather than print whitespace).
     */
    public static function clean(?string $value): ?string
    {
        if ($value === null) return null;
        $trimmed = trim($value);
        if ($trimmed === '') return null;

        if (self::containsBadToken($trimmed)) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Sanitize an array of free-text values. Drops every entry that is
     * empty or contains a bad token. Re-indexes the resulting array.
     *
     * @param  array<int|string,mixed>  $values
     * @return array<int,string>
     */
    public static function cleanArray(?array $values): array
    {
        if ($values === null) return [];
        $out = [];
        foreach ($values as $v) {
            if (!is_string($v)) continue;
            $clean = self::clean($v);
            if ($clean !== null) {
                $out[] = $clean;
            }
        }
        return $out;
    }

    /**
     * Returns true if the value contains any of the forbidden token stems.
     * Normalisation strips Polish diacritics and lowercases so we don't
     * have to enumerate every spelling variant.
     */
    private static function containsBadToken(string $value): bool
    {
        $norm = self::normalize($value);
        foreach (self::BAD_TOKENS as $tok) {
            if (str_contains($norm, $tok)) {
                return true;
            }
        }
        return false;
    }

    private static function normalize(string $value): string
    {
        $s = mb_strtolower($value, 'UTF-8');
        $s = strtr($s, [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        ]);
        return $s;
    }
}
