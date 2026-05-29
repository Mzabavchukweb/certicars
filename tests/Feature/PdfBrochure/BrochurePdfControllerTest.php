<?php

namespace Tests\Feature\PdfBrochure;

use App\Models\Brand;
use App\Models\Car;
use App\PdfBrochure\BrochureRenderException;
use App\PdfBrochure\ChromiumRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Route-level guarantees. Chromium is mocked so the test suite never tries
 * to spawn a real browser — the controller's job here is auth, plumbing,
 * headers and error handling.
 */
class BrochurePdfControllerTest extends TestCase
{
    use RefreshDatabase;

    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
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

    /**
     * Mock the renderer with both assertReady() (no-op) and render() so the
     * controller's pre-flight check passes and the test focuses on whatever
     * behaviour is under assertion.
     */
    private function mockChromium(string $pdf = "%PDF-1.4\n%mocked\n%%EOF"): void
    {
        $mock = Mockery::mock(ChromiumRenderer::class);
        $mock->shouldReceive('assertReady')->andReturn(null);
        $mock->shouldReceive('render')->andReturn($pdf);
        $this->app->instance(ChromiumRenderer::class, $mock);
    }

    public function test_happy_path_returns_real_pdf_with_attachment_header(): void
    {
        $this->mockChromium();
        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertOk();
        $r->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('attachment;', $r->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', $r->getContent(),
            'body must start with %PDF — this is the regression that produced download.html');
        $this->assertNotEmpty($r->headers->get('X-PDF-Report-Id'));
    }

    public function test_chromium_failure_returns_json_500_with_no_attachment(): void
    {
        $mock = Mockery::mock(ChromiumRenderer::class);
        $mock->shouldReceive('assertReady')->andReturn(null);
        $mock->shouldReceive('render')->andThrow(new BrochureRenderException('boom'));
        $this->app->instance(ChromiumRenderer::class, $mock);

        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertStatus(500);
        // CRITICAL: error path must NOT carry an attachment header. That
        // header was the root cause of the production "download.html"
        // incident — browsers in download mode save WHATEVER comes back.
        $this->assertNull($r->headers->get('Content-Disposition'),
            'error response must not set Content-Disposition: attachment');
        $r->assertHeader('Content-Type', 'application/json; charset=utf-8');
        $r->assertJsonStructure(['error', 'message', 'report_id']);
        $this->assertNotEmpty($r->headers->get('X-PDF-Report-Id'));
    }

    public function test_chromium_unavailable_fails_fast_with_json_500(): void
    {
        // assertReady() throws → controller never even attempts to embed
        // images. Customer gets a fast clean error instead of the
        // Browsershot 60-second hang.
        $mock = Mockery::mock(ChromiumRenderer::class);
        $mock->shouldReceive('assertReady')->andThrow(
            new BrochureRenderException('Chromium binary not found')
        );
        $mock->shouldNotReceive('render');
        $this->app->instance(ChromiumRenderer::class, $mock);

        $r = $this->get(route('car.pdf', $this->car->slug));

        $r->assertStatus(500);
        $r->assertHeader('Content-Type', 'application/json; charset=utf-8');
        $this->assertNull($r->headers->get('Content-Disposition'));
        $r->assertJsonPath('error', 'PDF generation failed.');
    }

    public function test_renderer_returning_non_pdf_bytes_is_caught_before_response(): void
    {
        // Belt-and-braces guard: if the renderer somehow returns garbage,
        // the controller's "starts with %PDF" check must reject it before
        // those bytes ship to the customer as a fake PDF download.
        $this->mockChromium("not actually a pdf body");

        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertStatus(500);
        $r->assertHeader('Content-Type', 'application/json; charset=utf-8');
        $this->assertNull($r->headers->get('Content-Disposition'));
    }

    public function test_inactive_car_returns_404_to_anonymous(): void
    {
        $this->mockChromium();
        $this->car->update(['status' => 'draft']);
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertNotFound();
    }

    public function test_certicheck_disabled_returns_404_to_anonymous(): void
    {
        $this->mockChromium();
        $this->car->update(['has_certicheck' => false]);
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertNotFound();
    }

    public function test_diagnostic_endpoint_blocks_anonymous(): void
    {
        $r = $this->get(route('admin.cars.pdf.diagnostic', $this->car->id));
        // login redirect or 403 — admin gate.
        $this->assertContains($r->getStatusCode(), [302, 401, 403]);
    }

    public function test_health_endpoint_blocks_anonymous(): void
    {
        $r = $this->get(route('admin.pdf.health'));
        $this->assertContains($r->getStatusCode(), [302, 401, 403]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
