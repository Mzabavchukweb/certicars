<?php

namespace App\PdfBrochure;

/**
 * Single chokepoint for every admin-typed free-text field that the brochure
 * surfaces to a client. A field that contains profanity, test placeholders,
 * or internal markers is dropped wholesale — `clean()` returns `null` and
 * the caller treats null as "hide this field entirely". Whitespace-only or
 * empty inputs collapse the same way.
 *
 * The token list intentionally stays small. Building a full Polish-profanity
 * filter is admin-validation work; this is a last line of defence for the
 * specific failures that have actually leaked into shipped brochures.
 */
final class TextSanitizer
{
    /**
     * Lowercase, diacritic-stripped stems. Match is "contains" against a
     * normalised version of the input, so a single entry catches all suffix
     * variants (zajebist matches zajebiste / zajebista / zajebisty).
     */
    private const BAD_STEMS = [
        // Polish profanity / strong slang stems. "zajebis" catches both
        // "zajebist*" (suffix a/e/y) and "zajebiście" (which normalises to
        // "zajebiscie" — has no 't' so the longer stem missed it).
        'zajebis',
        'jebac',
        'kurwa', 'kurwy',
        'chuj', 'chujow',
        'pierdol', 'pierdole', 'pierdolony',
        'spierdal',
        'cipa', 'cipy',
        'dupa',
        'pizd',
        'skurwy', 'skurwi',
        // Test placeholders that have shown up in historical brochures.
        'asdf', 'qwerty', 'lorem', 'ipsum', 'test123', 'xxx', 'aaa', 'zzz',
        // Internal/dev markers.
        'todo', 'fixme', 'tbd', 'tba', 'wip',
    ];

    /**
     * Sanitize one free-text value.
     *
     * @return string|null  Trimmed original if clean, null if empty or dirty.
     */
    public static function clean(?string $value): ?string
    {
        if ($value === null) return null;
        $trimmed = trim($value);
        if ($trimmed === '') return null;
        if (self::isDirty($trimmed)) return null;
        return $trimmed;
    }

    /**
     * Sanitize an array of free-text values. Drops empty entries, dirty
     * entries, and non-string entries. Result is re-indexed from zero.
     *
     * @param  iterable<mixed>|null $values
     * @return array<int,string>
     */
    public static function cleanArray(?iterable $values): array
    {
        if ($values === null) return [];
        $out = [];
        foreach ($values as $v) {
            if (!is_string($v)) continue;
            $clean = self::clean($v);
            if ($clean !== null) $out[] = $clean;
        }
        return $out;
    }

    /** Public helper for tests / callers that need a boolean. */
    public static function isDirty(string $value): bool
    {
        $norm = self::normalize($value);
        foreach (self::BAD_STEMS as $stem) {
            if (str_contains($norm, $stem)) return true;
        }
        return false;
    }

    private static function normalize(string $value): string
    {
        return strtr(mb_strtolower($value, 'UTF-8'), [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        ]);
    }
}
