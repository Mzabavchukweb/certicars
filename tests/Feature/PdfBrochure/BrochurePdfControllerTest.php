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

    private function mockChromium(string $pdf = "%PDF-1.4\n%mocked\n%%EOF"): void
    {
        $mock = Mockery::mock(ChromiumRenderer::class);
        $mock->shouldReceive('render')->andReturn($pdf);
        $this->app->instance(ChromiumRenderer::class, $mock);
    }

    public function test_route_returns_application_pdf(): void
    {
        $this->mockChromium();
        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertOk();
        $r->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($r->headers->get('X-PDF-Report-Id'));
    }

    public function test_route_returns_500_when_chromium_throws(): void
    {
        $mock = Mockery::mock(ChromiumRenderer::class);
        $mock->shouldReceive('render')->andThrow(new BrochureRenderException('boom'));
        $this->app->instance(ChromiumRenderer::class, $mock);

        $r = $this->get(route('car.pdf', $this->car->slug));
        $r->assertStatus(500);
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
