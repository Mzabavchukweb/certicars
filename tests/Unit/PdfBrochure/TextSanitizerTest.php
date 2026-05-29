<?php

namespace Tests\Unit\PdfBrochure;

use App\PdfBrochure\TextSanitizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The sanitizer is the last line of defence before admin free-text reaches a
 * client PDF. Every regression here is something that has ACTUALLY shipped
 * to production in the past — keep the list pinned.
 */
class TextSanitizerTest extends TestCase
{
    #[Test]
    public function it_returns_null_for_empty_or_whitespace(): void
    {
        $this->assertNull(TextSanitizer::clean(null));
        $this->assertNull(TextSanitizer::clean(''));
        $this->assertNull(TextSanitizer::clean('   '));
        $this->assertNull(TextSanitizer::clean("\n\t  "));
    }

    #[Test]
    public function it_passes_through_clean_input_trimmed(): void
    {
        $this->assertSame('215/55 R17 94V',     TextSanitizer::clean('  215/55 R17 94V  '));
        $this->assertSame('Felgi aluminiowe 17"', TextSanitizer::clean('Felgi aluminiowe 17"'));
        $this->assertSame('Bardzo dobry stan',  TextSanitizer::clean('Bardzo dobry stan'));
    }

    #[Test]
    public function it_blocks_polish_profanity_stems(): void
    {
        // Every spelling variant we've seen leak.
        $this->assertNull(TextSanitizer::clean('zajebiste'));
        $this->assertNull(TextSanitizer::clean('zajebista'));
        $this->assertNull(TextSanitizer::clean('zajebisty'));
        $this->assertNull(TextSanitizer::clean('kurwa testowe felgi'));
        $this->assertNull(TextSanitizer::clean('chujowe opony'));
        $this->assertNull(TextSanitizer::clean('spierdalaj'));
        // Diacritics shouldn't help anyone bypass the filter.
        $this->assertNull(TextSanitizer::clean('zajebiśćie'));
    }

    #[Test]
    public function it_blocks_test_placeholders(): void
    {
        $this->assertNull(TextSanitizer::clean('asdf'));
        $this->assertNull(TextSanitizer::clean('lorem ipsum dolor'));
        $this->assertNull(TextSanitizer::clean('test123'));
        $this->assertNull(TextSanitizer::clean('xxx'));
    }

    #[Test]
    public function it_blocks_dev_markers(): void
    {
        $this->assertNull(TextSanitizer::clean('TODO write description'));
        $this->assertNull(TextSanitizer::clean('FIXME later'));
        $this->assertNull(TextSanitizer::clean('TBD'));
    }

    #[Test]
    public function clean_array_drops_dirty_entries_and_reindexes(): void
    {
        $result = TextSanitizer::cleanArray(['rysa', 'zajebiste', 'otarcie']);
        $this->assertSame(['rysa', 'otarcie'], $result);
        // Non-string entries dropped silently.
        $result = TextSanitizer::cleanArray(['ok', 123, null, 'good', 'asdf']);
        $this->assertSame(['ok', 'good'], $result);
        // Null / empty arrays handled.
        $this->assertSame([], TextSanitizer::cleanArray(null));
        $this->assertSame([], TextSanitizer::cleanArray([]));
    }

    #[Test]
    public function is_dirty_helper_works_for_caller_branching(): void
    {
        $this->assertTrue(TextSanitizer::isDirty('zajebiste'));
        $this->assertTrue(TextSanitizer::isDirty('something kurwa something'));
        $this->assertFalse(TextSanitizer::isDirty('Renault Espace'));
        $this->assertFalse(TextSanitizer::isDirty('OK'));
    }
}
