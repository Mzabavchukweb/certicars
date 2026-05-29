<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use App\Services\BrochurePdfRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Regression coverage for the public CertiCheck brochure PDF download.
 *
 * These tests do NOT spawn Chromium. They mock the BrochurePdfRenderer so
 * we can inspect the HTML that *would* be handed to the browser. Each test
 * pins one client-facing contract that previously regressed:
 *  - missing images don't emit empty <img> tags
 *  - raw enum keys (front_left / zajebiste) never reach the rendered HTML
 *  - the route returns application/pdf
 *  - the controller falls back to DomPDF when Browsershot throws
 *
 * The end-to-end "real Chromium produces a real PDF with real R2 images"
 * verification is manual (screenshots attached to the PR).
 */
class BrochurePdfTest extends TestCase
{
    use RefreshDatabase;

    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $this->car = Car::create([
            'brand_id'          => $brand->id,
            'model'             => 'X5 Test',
            'price'             => 250000,
            'currency'          => 'PLN',
            'status'            => 'active',
            'is_sold'           => false,
            'has_certicheck'    => true,
            'mileage'           => 80000,
            'first_registration'=> '2021',
            'fuel_type'         => 'diesel',
            'transmission'      => 'automatic',
        ]);
    }

    /** Capture HTML that would be sent to Chromium without actually running it. */
    private function captureBrowserHtml(): string
    {
        $captured = '';
        $mock = Mockery::mock(BrochurePdfRenderer::class);
        $mock->shouldReceive('render')
            ->andReturnUsing(function (string $html) use (&$captured) {
                $captured = $html;
                // Return minimal valid PDF bytes so the controller continues.
                return "%PDF-1.4\n%minimal\n%%EOF";
            });
        $this->app->instance(BrochurePdfRenderer::class, $mock);

        $resp = $this->get(route('car.pdf', $this->car->slug));
        $resp->assertOk();

        return $captured;
    }

    public function test_pdf_route_returns_pdf_content_type(): void
    {
        // Mock the renderer so the test doesn't spawn Chromium.
        $mock = Mockery::mock(BrochurePdfRenderer::class);
        $mock->shouldReceive('render')->andReturn("%PDF-1.4\n%test\n%%EOF");
        $this->app->instance(BrochurePdfRenderer::class, $mock);

        $resp = $this->get(route('car.pdf', $this->car->slug));

        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'application/pdf');
        $resp->assertHeader('X-PDF-Renderer', 'browser');
    }

    public function test_pdf_falls_back_to_dompdf_when_browsershot_throws(): void
    {
        $mock = Mockery::mock(BrochurePdfRenderer::class);
        $mock->shouldReceive('render')
            ->andThrow(new \RuntimeException('chromium missing'));
        $this->app->instance(BrochurePdfRenderer::class, $mock);

        $resp = $this->get(route('car.pdf', $this->car->slug));

        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'application/pdf');
        $resp->assertHeader('X-PDF-Renderer', 'dompdf_fallback');
    }

    public function test_brochure_does_not_emit_empty_img_for_unreachable_photos(): void
    {
        // Create a CarImage whose path will fail HEAD validation (made-up host).
        CarImage::create([
            'car_id'     => $this->car->id,
            'path'       => 'https://invalid.test.example.invalid/never.jpg',
            'type'       => 'gallery',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $html = $this->captureBrowserHtml();

        // The brochure HTML must NOT contain <img src="" or <img alt=...> with
        // no src. Both are signals that a missing image leaked through as an
        // empty placeholder element.
        $this->assertStringNotContainsString('<img src=""', $html);
        $this->assertStringNotContainsString("<img src='''", $html);
        // Negative match on the actual invalid URL — proves the validator
        // rejected it before it reached the template.
        $this->assertStringNotContainsString('invalid.test.example.invalid', $html);
    }

    public function test_brochure_maps_raw_tire_enum_keys_to_polish_labels(): void
    {
        // Tire sets aren't on the base car factory — create one inline with
        // a raw enum position to assert the helper kicks in.
        $set = \App\Models\CarTireSet::create([
            'car_id'     => $this->car->id,
            'set_number' => 1,
            'tire_type'  => 'Letnia',
        ]);
        \App\Models\CarTire::create([
            'car_tire_set_id' => $set->id,
            'position'        => 'front_left',
            'tread_depth'     => 6.5,
            'condition'       => ['zajebiste'], // admin typo / slang
        ]);

        $html = $this->captureBrowserHtml();

        // Raw enum key + raw admin typo MUST NOT appear in the client PDF.
        $this->assertStringNotContainsString('front_left', $html, 'raw enum key leaked into PDF HTML');
        $this->assertStringNotContainsString('zajebiste', $html, 'raw admin typo leaked into PDF HTML');
        // Mapped labels MUST appear.
        $this->assertStringContainsString('Przednia lewa', $html);
        // Unknown condition → "Wymaga uwagi" fallback, not the raw text.
        $this->assertStringContainsString('Wymaga uwagi', $html);
    }

    public function test_brochure_skips_photo_grid_section_when_no_valid_images(): void
    {
        // No CarImages at all → gallery + damage chunks are empty → photo
        // grid header should not render (would leave an orphan section
        // header at the bottom of an otherwise empty page).
        $html = $this->captureBrowserHtml();

        $this->assertStringNotContainsString('Dokumentacja fotograficzna', $html);
        $this->assertStringNotContainsString('Zdjęcia uszkodzeń', $html);
    }

    public function test_carlabels_tire_position_helper_maps_known_keys(): void
    {
        $this->assertSame('Przednia lewa',  \App\Helpers\CarLabels::tirePosition('front_left'));
        $this->assertSame('Przednia prawa', \App\Helpers\CarLabels::tirePosition('front_right'));
        $this->assertSame('Tylna lewa',     \App\Helpers\CarLabels::tirePosition('rear_left'));
        $this->assertSame('Tylna prawa',    \App\Helpers\CarLabels::tirePosition('rear_right'));
        $this->assertSame('Zapasowe',       \App\Helpers\CarLabels::tirePosition('spare'));
        // Unknown keys: title-case clean-up, never raw underscores.
        $this->assertStringNotContainsString('_', \App\Helpers\CarLabels::tirePosition('front_extra'));
        // Null/empty → safe dash.
        $this->assertSame('—', \App\Helpers\CarLabels::tirePosition(null));
        $this->assertSame('—', \App\Helpers\CarLabels::tirePosition(''));
    }

    public function test_carlabels_tire_condition_helper_classifies_known_cases(): void
    {
        // Replacement language → "Do wymiany" (bad).
        $bad = \App\Helpers\CarLabels::tireCondition(['do wymiany']);
        $this->assertSame('Do wymiany', $bad['label']);
        $this->assertSame('cond-bad',   $bad['class']);

        // Worn → "Wymaga uwagi" (warn).
        $worn = \App\Helpers\CarLabels::tireCondition(['zużyta opona']);
        $this->assertSame('Wymaga uwagi', $worn['label']);

        // Empty → "Stan bardzo dobry" (ok).
        $empty = \App\Helpers\CarLabels::tireCondition([]);
        $this->assertSame('Stan bardzo dobry', $empty['label']);
        $this->assertSame('cond-ok',           $empty['class']);

        // "ok" / "dobry" → "Dobry".
        $ok = \App\Helpers\CarLabels::tireCondition(['ok']);
        $this->assertSame('Dobry', $ok['label']);

        // Unknown text → safe fallback "Wymaga uwagi", never echoes raw.
        $unknown = \App\Helpers\CarLabels::tireCondition(['zajebiste']);
        $this->assertSame('Wymaga uwagi', $unknown['label']);
    }

    public function test_carlabels_damage_location_helper_maps_known_keys(): void
    {
        $this->assertSame('Przód lewy',          \App\Helpers\CarLabels::damageLocation('front_left'));
        $this->assertSame('Tył prawy',           \App\Helpers\CarLabels::damageLocation('rear_right'));
        $this->assertSame('Maska',               \App\Helpers\CarLabels::damageLocation('hood'));
        $this->assertSame('Klapa bagażnika',     \App\Helpers\CarLabels::damageLocation('trunk'));
        $this->assertSame('Drzwi przednie lewe', \App\Helpers\CarLabels::damageLocation('door_front_left'));
        $this->assertSame('—',                   \App\Helpers\CarLabels::damageLocation(null));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
