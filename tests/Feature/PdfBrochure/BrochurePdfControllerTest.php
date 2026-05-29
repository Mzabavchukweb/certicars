<?php

namespace Tests\Feature\PdfBrochure;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Public download route now serves the cached file ONLY. No Chromium, no
 * synchronous render at request time. Tests pin the behaviour:
 *
 *   ready=true  → 200 + application/pdf + attachment + %PDF body
 *   ready=false → 404, NO attachment, browser shows normal Not Found
 *
 * The "no attachment on the not-ready path" assertion is the single most
 * important regression guard in this file: that header was what made
 * Chrome save the response body as `download.json` (and before, `.html`).
 */
class BrochurePdfControllerTest extends TestCase
{
    use RefreshDatabase;

    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $this->car = Car::create([
            'brand_id'       => $brand->id,
            'model'          => 'Espace',
            'price'          => 48900,
            'currency'       => 'PLN',
            'status'         => 'active',
            'is_sold'        => false,
            'has_certicheck' => true,
            'mileage'        => 136000,
            'fuel_type'      => 'diesel',
            'transmission'   => 'automatic',
        ]);
    }

    /** Pretend a successful regeneration has already run for this car. */
    private function pretendBrochureReady(string $contents = "%PDF-1.4\n%mocked\n%%EOF"): void
    {
        $path = sprintf('brochures/%d/certicheck.pdf', $this->car->id);
        Storage::disk('public')->put($path, $contents);
        $this->car->forceFill([
            'brochure_status'       => 'ready',
            'brochure_path'         => $path,
            'brochure_size'         => strlen($contents),
            'brochure_generated_at' => now(),
            'brochure_error'        => null,
        ])->saveQuietly();
    }

    public function test_ready_brochure_returns_real_pdf_with_attachment_header(): void
    {
        $this->pretendBrochureReady();

        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertOk();
        $r->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('attachment;', $r->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', $r->getContent());
        $this->assertNotEmpty($r->headers->get('X-PDF-Report-Id'));
    }

    public function test_brochure_missing_returns_404_with_no_attachment_header(): void
    {
        // brochure_status defaults to 'missing' — file never written. The
        // CRITICAL assertion: no Content-Disposition: attachment. That is
        // the only way to prevent Chrome from saving a 404 response body
        // as `download.json`.
        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertStatus(404);
        $this->assertNull($r->headers->get('Content-Disposition'),
            'NEVER ship attachment header on the not-ready path — that is the download.html/.json bug.');
    }

    public function test_brochure_generating_returns_404_with_no_attachment(): void
    {
        $this->car->forceFill(['brochure_status' => 'generating'])->saveQuietly();
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertStatus(404);
        $this->assertNull($r->headers->get('Content-Disposition'));
    }

    public function test_brochure_failed_returns_404_with_no_attachment(): void
    {
        $this->car->forceFill([
            'brochure_status' => 'failed',
            'brochure_error'  => 'chromium oom',
        ])->saveQuietly();
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertStatus(404);
        $this->assertNull($r->headers->get('Content-Disposition'));
    }

    public function test_ready_status_but_file_gone_returns_404_and_marks_missing(): void
    {
        // DB thinks the brochure is ready but the underlying file was
        // deleted out-of-band. Controller must self-heal the status row.
        $this->car->forceFill([
            'brochure_status' => 'ready',
            'brochure_path'   => 'brochures/' . $this->car->id . '/certicheck.pdf',
        ])->saveQuietly();
        // Note: pretendBrochureReady NOT called → no file written.

        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertStatus(404);
        $this->assertNull($r->headers->get('Content-Disposition'));
        $this->assertSame('missing', $this->car->fresh()->brochure_status);
    }

    public function test_ready_status_but_cached_bytes_not_pdf_returns_404_and_marks_failed(): void
    {
        // Cached file got corrupted somehow. Controller must reject and
        // flip status to 'failed' rather than ship garbage as a PDF.
        $path = sprintf('brochures/%d/certicheck.pdf', $this->car->id);
        Storage::disk('public')->put($path, 'this is not a pdf');
        $this->car->forceFill([
            'brochure_status' => 'ready',
            'brochure_path'   => $path,
        ])->saveQuietly();

        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertStatus(404);
        $this->assertNull($r->headers->get('Content-Disposition'));
        $this->assertSame('failed', $this->car->fresh()->brochure_status);
    }

    public function test_inactive_car_returns_404_to_anonymous(): void
    {
        $this->pretendBrochureReady();
        $this->car->forceFill(['status' => 'draft'])->saveQuietly();
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertNotFound();
    }

    public function test_certicheck_disabled_returns_404_to_anonymous(): void
    {
        $this->pretendBrochureReady();
        $this->car->forceFill(['has_certicheck' => false])->saveQuietly();
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertNotFound();
    }

    public function test_diagnostic_endpoint_blocks_anonymous(): void
    {
        $r = $this->get(route('admin.cars.pdf.diagnostic', $this->car->id));
        $this->assertContains($r->getStatusCode(), [302, 401, 403]);
    }

    public function test_health_endpoint_blocks_anonymous(): void
    {
        $r = $this->get(route('admin.pdf.health'));
        $this->assertContains($r->getStatusCode(), [302, 401, 403]);
    }

    public function test_regenerate_endpoint_blocks_anonymous(): void
    {
        $r = $this->post(route('admin.cars.pdf.regenerate', $this->car->id));
        $this->assertContains($r->getStatusCode(), [302, 401, 403, 419]);
    }
}
