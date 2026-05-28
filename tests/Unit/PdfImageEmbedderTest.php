<?php

namespace Tests\Unit;

use App\Services\PdfImageEmbedder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Production-style coverage for the PDF image embedding pipeline. Each test
 * targets one failure mode that previously made photos vanish from the
 * downloaded brochure: invalid path, unsupported format, junk data, WebP
 * needing conversion, etc.
 *
 * The R2 public-URL HTTP path is exercised by manual production verification
 * (X-PDF-Report-Id header + Railway log correlation) — unit-testing that
 * branch would need a real local HTTP fixture which is out of scope.
 *
 * Static helpers (writing real image bytes via GD) keep the tests fully
 * self-contained — no fixture asset files committed.
 */
class PdfImageEmbedderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function it_resolves_a_valid_local_image_to_its_on_disk_path(): void
    {
        $bytes = $this->jpegBytes(40, 30);
        Storage::disk('public')->put('cars/1/hero.jpg', $bytes);

        $embedder = new PdfImageEmbedder('rid', null, false);
        $local = $embedder->resolveToLocalFile('cars/1/hero.jpg', 'hero');

        $this->assertNotNull($local, 'embedder must return a local path');
        $this->assertFileExists($local);
        $this->assertSame('image/jpeg', mime_content_type($local));
        // No temp file: local disk path passes through directly.
        $this->assertSame(0, $embedder->tmpFileCount());
        $this->assertSame(1, $embedder->stats()['success']);
    }

    #[Test]
    public function it_returns_null_and_does_not_throw_when_path_is_empty_or_blank(): void
    {
        $embedder = new PdfImageEmbedder('rid', null, false);

        $this->assertNull($embedder->resolveToLocalFile(null, 'hero'));
        $this->assertNull($embedder->resolveToLocalFile('', 'hero'));
        $this->assertNull($embedder->resolveToLocalFile('   ', 'hero'));
        $this->assertSame(0, $embedder->stats()['success']);
    }

    #[Test]
    public function it_returns_null_when_local_disk_file_is_missing(): void
    {
        $embedder = new PdfImageEmbedder('rid', null, false);
        $local = $embedder->resolveToLocalFile('cars/9999/nope.jpg', 'gallery');
        $this->assertNull($local);
        $this->assertSame(1, $embedder->stats()['failed']);
    }

    #[Test]
    public function it_rejects_a_local_file_that_is_not_an_image(): void
    {
        Storage::disk('public')->put('cars/2/notes.txt', 'just plain text masquerading as a jpg');
        $embedder = new PdfImageEmbedder('rid', null, false);
        $this->assertNull($embedder->resolveToLocalFile('cars/2/notes.txt', 'gallery'));
        $this->assertSame(1, $embedder->stats()['failed']);
    }

    #[Test]
    public function it_converts_a_webp_local_file_to_jpeg_for_dompdf(): void
    {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP support not available on this PHP build');
        }
        $im = imagecreatetruecolor(50, 40);
        $tmpWebp = tempnam(sys_get_temp_dir(), 'cc_webp_');
        imagewebp($im, $tmpWebp);
        imagedestroy($im);
        $bytes = (string) file_get_contents($tmpWebp);
        @unlink($tmpWebp);
        Storage::disk('public')->put('cars/3/photo.webp', $bytes);

        $embedder = new PdfImageEmbedder('rid', null, false);
        $local = $embedder->resolveToLocalFile('cars/3/photo.webp', 'gallery');

        $this->assertNotNull($local, 'WebP must be converted, not rejected');
        $this->assertSame('image/jpeg', mime_content_type($local));
        $this->assertSame(1, $embedder->stats()['success']);
        $this->assertSame(1, $embedder->tmpFileCount());
    }

    #[Test]
    public function it_caches_repeated_resolves_for_the_same_path(): void
    {
        $bytes = $this->jpegBytes(20, 20);
        Storage::disk('public')->put('cars/4/dup.jpg', $bytes);

        $embedder = new PdfImageEmbedder('rid', null, false);
        $first  = $embedder->resolveToLocalFile('cars/4/dup.jpg', 'hero');
        $second = $embedder->resolveToLocalFile('cars/4/dup.jpg', 'gallery');

        $this->assertSame($first, $second);
        $this->assertSame(1, $embedder->stats()['success']);
        $this->assertSame(1, $embedder->stats()['cached']);
    }

    #[Test]
    public function cleanup_removes_temp_files_created_by_webp_conversion(): void
    {
        if (!function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP support not available');
        }
        $im = imagecreatetruecolor(60, 50);
        $tmpWebp = tempnam(sys_get_temp_dir(), 'cc_webp_');
        imagewebp($im, $tmpWebp);
        imagedestroy($im);
        $bytes = (string) file_get_contents($tmpWebp);
        @unlink($tmpWebp);
        Storage::disk('public')->put('cars/5/x.webp', $bytes);

        $embedder = new PdfImageEmbedder('rid', null, false);
        $local = $embedder->resolveToLocalFile('cars/5/x.webp', 'gallery');
        $this->assertNotNull($local);
        $this->assertFileExists($local);

        $embedder->cleanup();
        $this->assertFileDoesNotExist($local, 'cleanup must delete the temp file');
        $this->assertSame(0, $embedder->tmpFileCount());
    }

    #[Test]
    public function it_rejects_a_zero_byte_file_on_disk(): void
    {
        Storage::disk('public')->put('cars/6/empty.jpg', '');
        $embedder = new PdfImageEmbedder('rid', null, false);
        $this->assertNull($embedder->resolveToLocalFile('cars/6/empty.jpg', 'gallery'));
        $this->assertSame(1, $embedder->stats()['failed']);
    }

    #[Test]
    public function generated_pdf_for_a_car_with_a_real_jpeg_embeds_the_image_stream(): void
    {
        // End-to-end: render the actual PDF via the controller and verify the
        // resulting bytes contain a JPEG embedded image stream — the symptom
        // we'd see in production when photos work vs broken-X placeholders.
        $brand = \App\Models\Brand::create(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $car = \App\Models\Car::create([
            'brand_id'       => $brand->id,
            'model'          => 'PdfTest',
            'price'          => 12345,
            'currency'       => 'PLN',
            'status'         => 'active',
            'is_sold'        => false,
            'has_certicheck' => true,
        ]);
        // Real JPEG on the fake public disk; CarImage relation will surface it.
        $bytes = $this->jpegBytes(120, 80);
        Storage::disk('public')->put('cars/' . $car->id . '/photo.jpg', $bytes);
        \App\Models\CarImage::create([
            'car_id'     => $car->id,
            'path'       => 'cars/' . $car->id . '/photo.jpg',
            'alt'        => 'test',
            'type'       => 'gallery',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('car.pdf', $car->fresh()->slug));
        $response->assertOk();
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        // Every DomPDF-embedded image stream contains the /XObject /Subtype /Image
        // marker. If the embedder rejected our test JPEG we wouldn't see it.
        $this->assertStringContainsString('/Subtype /Image', $content,
            'rendered PDF should contain at least one embedded image stream');
        // Trace id round-trips so production logs are debuggable.
        $this->assertNotEmpty($response->headers->get('X-PDF-Report-Id'));
    }

    // ─── helpers ────────────────────────────────────────────────────────────

    /** Returns valid JPEG bytes of the requested dimensions. */
    private function jpegBytes(int $w, int $h): string
    {
        $im = imagecreatetruecolor($w, $h);
        imagefill($im, 0, 0, imagecolorallocate($im, 200, 200, 200));
        $tmp = tempnam(sys_get_temp_dir(), 'cc_jpeg_');
        imagejpeg($im, $tmp, 90);
        imagedestroy($im);
        $bytes = (string) file_get_contents($tmp);
        @unlink($tmp);
        return $bytes;
    }
}
