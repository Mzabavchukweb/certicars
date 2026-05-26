<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDamage;
use App\Models\CarImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for the CertiCheck report page rebuild.
 *
 * Spec checkpoints (mapped 1:1 to the user's request):
 *   1. Page returns 200 for a certified, active car.
 *   2. Primary car image renders at the top of the report.
 *   3. Damage photos are rendered when the damage carries an image.
 *   4. Damage photo URLs use the R2-aware accessor — never asset('storage/'.path).
 *   5. Fuel consumption renders the canonical "X l/100 km" with no duplicated unit
 *      even when the admin typed the unit into the field.
 *   6. The global .float-call phone FAB is hidden on the report.
 *   7. The PDF download route still returns 200 for the same car.
 */
class CertiCheckReportTest extends TestCase
{
    use RefreshDatabase;

    private function certifiedCar(array $overrides = []): Car
    {
        $brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
        return Car::create(array_merge([
            'brand_id'       => $brand->id,
            'model'          => 'A4',
            'slug'           => 'audi-a4-test',
            'status'         => 'active',
            'has_certicheck' => true,
        ], $overrides));
    }

    public function test_certicheck_page_returns_200_for_certified_car(): void
    {
        $car = $this->certifiedCar();
        $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk();
    }

    public function test_certicheck_renders_primary_car_image_when_gallery_exists(): void
    {
        $car = $this->certifiedCar();
        // Use a full https:// path so the CarImage::url accessor short-circuits
        // and doesn't try to look up a non-existent file on the test filesystem
        // — the goal here is to prove the hero <img> renders, not that local-
        // disk existence checks work.
        CarImage::create([
            'car_id'     => $car->id,
            'type'       => 'gallery',
            'path'       => 'https://cdn.example.test/cars/'.$car->id.'/gallery/cover.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();

        $this->assertStringContainsString('cc-hero',                       $html, 'CertiCheck must render the hero image container.');
        $this->assertStringContainsString('cdn.example.test',              $html, 'Primary image URL must appear in the hero <img>.');
        $this->assertStringContainsString('cover.jpg',                     $html, 'Primary image filename must appear.');
        $this->assertStringContainsString('alt="'.e($car->title).'"',      $html, 'Hero image must carry the car title as alt text.');
    }

    public function test_certicheck_shows_placeholder_when_no_primary_image(): void
    {
        $car = $this->certifiedCar();
        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();
        $this->assertStringContainsString('Brak zdjęcia pojazdu', $html);
    }

    public function test_certicheck_renders_damage_photos_when_damage_has_image(): void
    {
        $car = $this->certifiedCar();
        CarDamage::create([
            'car_id'      => $car->id,
            'area'        => 'Przedni zderzak',
            'type'        => 'damage',
            'severity'    => 'low',
            'description' => 'Drobne otarcie.',
            // Full https:// path so the CarDamage::image_url accessor short-circuits.
            'image_path'  => 'https://cdn.example.test/cars/'.$car->id.'/damages/scratch.jpg',
            'position_x'  => 30,
            'position_y'  => 60,
            'tags'        => ['otarcie'],
        ]);

        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();

        $this->assertStringContainsString('cc-dmg-photos',  $html, 'CertiCheck must render the per-damage photo grid.');
        $this->assertStringContainsString('scratch.jpg',    $html, 'Damage image path must appear in the report.');
        $this->assertStringContainsString('Przedni zderzak', $html);
    }

    public function test_certicheck_damage_photo_url_uses_r2_aware_accessor_not_asset_storage(): void
    {
        // Source-grep guard mirroring tests/Feature/MediaCspDamagePhotosTest.php
        // (PR #7). Building S3 URLs end-to-end requires region/key/secret which
        // are env-dependent; the accessor itself is already covered there. Here
        // we just guarantee the report view uses the right accessors and never
        // the broken asset('storage/'.path) pattern.
        $source = file_get_contents(base_path('resources/views/catalog/certicheck.blade.php'));

        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$d->image_path\s*\)/",
            $source,
            "CertiCheck must use \$d->image_url accessor — asset('storage/...') 404s on R2 in production."
        );
        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$dp->path\s*\)/",
            $source,
            "CertiCheck must use \$dp->url accessor — asset('storage/...') 404s on R2 in production."
        );
        $this->assertStringContainsString('$d->image_url', $source,
            'CertiCheck must read damage main image via $d->image_url accessor.');
        $this->assertStringContainsString('$dp->url',      $source,
            'CertiCheck must read additional damage photos via $dp->url accessor.');
    }

    public function test_certicheck_fuel_consumption_renders_once_and_normalised(): void
    {
        // Admin typed the unit into the field — duplicated render is a regression.
        $car = $this->certifiedCar(['fuel_consumption' => '5,6 L/100km']);

        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();

        $this->assertStringContainsString('5.6 l/100 km',          $html, 'Fuel value must be normalised to "5.6 l/100 km".');
        $this->assertStringNotContainsString('L/100km l/100km',    $html);
        $this->assertStringNotContainsString('l/100km l/100km',    $html);
        $this->assertStringNotContainsString('l/100 km l/100 km',  $html);
    }

    public function test_certicheck_emission_class_normalised_to_euro(): void
    {
        $car = $this->certifiedCar(['emission_class' => 'euro 6']);
        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();
        $this->assertStringContainsString('Euro 6', $html);
    }

    public function test_certicheck_hides_floating_phone_widget(): void
    {
        $car = $this->certifiedCar();
        $html = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();
        // The float-call element is still emitted by the global layout, but the
        // page-local <style> must take it out of the flow on both screen and print.
        $this->assertStringContainsString('.float-call{display:none!important}', $html,
            'CertiCheck must hide the global floating phone FAB on the report.');
    }

    public function test_pdf_brochure_route_returns_200_for_certified_car(): void
    {
        $car = $this->certifiedCar();
        $response = $this->get('/samochody/'.$car->slug.'/pdf');
        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }

    public function test_pdf_brochure_view_consumes_pdf_src_not_raw_url(): void
    {
        // Source-grep regression guard: DomPDF runs with isRemoteEnabled=false in
        // PdfController, so R2 URLs silently fail. The view must consume the
        // pdf_src attribute the controller decorates onto every CarImage.
        $source = file_get_contents(base_path('resources/views/pdf/brochure.blade.php'));

        $this->assertStringContainsString('pdf_src', $source,
            'PDF brochure must reference $img->pdf_src — DomPDF cannot fetch raw R2 URLs.');

        // The old broken patterns (raw $img->url without a pdf_src guard) must be gone.
        $this->assertDoesNotMatchRegularExpression(
            '/<img src="\{\{\s*\$img->url\s*\}\}"/',
            $source,
            'PDF photo loops must use $img->pdf_src, never $img->url directly.'
        );
    }

    public function test_pdf_controller_decorates_all_image_collections(): void
    {
        // The three Eloquent relations (images, galleryImages, damageImages)
        // are loaded with separate queries → separate in-memory collections.
        // Decorating only one of them leaves photo-grid loops on the others
        // empty in the PDF (the bug reported on PR #16).
        $source = file_get_contents(base_path('app/Http/Controllers/PdfController.php'));

        $this->assertStringContainsString('$car->images->each',         $source);
        $this->assertStringContainsString('$car->galleryImages->each',  $source);
        $this->assertStringContainsString('$car->damageImages->each',   $source);
    }
}
