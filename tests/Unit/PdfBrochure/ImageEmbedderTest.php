<?php

namespace Tests\Unit\PdfBrochure;

use App\PdfBrochure\ImageEmbedder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Coverage of the failure modes that have shipped to production. Every test
 * pins one reason a brochure once arrived with a missing photo.
 */
class ImageEmbedderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function embedder(): ImageEmbedder
    {
        return new ImageEmbedder('rid', null, false);
    }

    /** Generate a real JPEG via GD so the test isn't fixture-coupled. */
    private function jpegBytes(int $w = 60, int $h = 40): string
    {
        $im = imagecreatetruecolor($w, $h);
        imagefilledrectangle($im, 0, 0, $w, $h, imagecolorallocate($im, 200, 100, 80));
        ob_start();
        imagejpeg($im, null, 85);
        $bytes = ob_get_clean();
        imagedestroy($im);
        return $bytes;
    }

    #[Test]
    public function it_embeds_a_valid_local_jpeg(): void
    {
        Storage::disk('public')->put('cars/1/hero.jpg', $this->jpegBytes(120, 80));

        $img = $this->embedder()->embed('cars/1/hero.jpg', 'hero');

        $this->assertNotNull($img);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $img->dataUri);
        $this->assertSame('cars/1/hero.jpg', $img->sourcePath);
        $this->assertGreaterThan(0, $img->width);
        $this->assertGreaterThan(0, $img->height);
        $this->assertGreaterThan(0, $img->bytes);
    }

    #[Test]
    public function it_skips_missing_local_path_cleanly(): void
    {
        $e = $this->embedder();
        $this->assertNull($e->embed('cars/1/nope.jpg', 'gallery'));
        $this->assertSame(1, $e->stats()['candidates']);
        $this->assertSame(1, $e->stats()['skipped']);
        $this->assertSame(0, $e->stats()['embedded']);
    }

    #[Test]
    public function it_skips_empty_or_null_path(): void
    {
        $e = $this->embedder();
        $this->assertNull($e->embed(null, 'gallery'));
        $this->assertNull($e->embed('', 'gallery'));
        $this->assertNull($e->embed('   ', 'gallery'));
    }

    #[Test]
    public function it_rejects_html_payload_masquerading_as_image(): void
    {
        // R2 sometimes returns a 200 OK HTML error page when an object's ACL
        // is wrong. The bytes have to be rejected even though HTTP status is OK.
        Storage::disk('public')->put('cars/1/broken.jpg', '<!DOCTYPE html><html><body>403</body></html>');
        $this->assertNull($this->embedder()->embed('cars/1/broken.jpg', 'gallery'));
    }

    #[Test]
    public function it_rejects_json_payload(): void
    {
        Storage::disk('public')->put('cars/1/broken.jpg', '{"error":"forbidden"}');
        $this->assertNull($this->embedder()->embed('cars/1/broken.jpg', 'gallery'));
    }

    #[Test]
    public function it_rejects_garbage_bytes(): void
    {
        Storage::disk('public')->put('cars/1/junk.jpg', str_repeat("\x00", 32));
        $this->assertNull($this->embedder()->embed('cars/1/junk.jpg', 'gallery'));
    }

    #[Test]
    public function it_rejects_unsupported_magic(): void
    {
        // First bytes don't match any known image format magic.
        Storage::disk('public')->put('cars/1/weird.jpg', "RANDOMBYTES" . str_repeat('x', 200));
        $this->assertNull($this->embedder()->embed('cars/1/weird.jpg', 'gallery'));
    }

    #[Test]
    public function duplicates_are_served_from_cache(): void
    {
        Storage::disk('public')->put('cars/1/hero.jpg', $this->jpegBytes());
        $e = $this->embedder();
        $first  = $e->embed('cars/1/hero.jpg', 'hero');
        $second = $e->embed('cars/1/hero.jpg', 'hero');

        $this->assertNotNull($first);
        $this->assertSame($first->dataUri, $second->dataUri);
        $this->assertSame(2, $e->stats()['candidates']);
        $this->assertSame(1, $e->stats()['embedded']);
        $this->assertSame(1, $e->stats()['cached']);
    }

    #[Test]
    public function manifest_records_every_attempt_with_outcome(): void
    {
        Storage::disk('public')->put('cars/1/ok.jpg', $this->jpegBytes());
        $e = $this->embedder();
        $e->embed('cars/1/ok.jpg', 'gallery');
        $e->embed('cars/1/missing.jpg', 'gallery');
        $e->embed('cars/1/bad.jpg', 'gallery');
        Storage::disk('public')->put('cars/1/bad.jpg', '<html>');
        $e->embed('cars/1/bad.jpg', 'gallery');

        $manifest = $e->manifest();
        $this->assertCount(4, $manifest);
        $outcomes = array_column($manifest, 'outcome');
        $this->assertContains('embedded', $outcomes);
        $this->assertContains('skipped',  $outcomes);
    }
}
