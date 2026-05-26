<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDamage;
use App\Models\CarImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * P1 regression suite — post-PR-#6 media display failures.
 *
 * Production evidence:
 *   1. Engine video blocked: "Loading media from 'https://pub-…r2.dev/cars/26/videos/…mov'
 *      violates the following Content Security Policy directive: default-src 'self'."
 *   2. Damage photo 404: 'M533EYYnv3FTxEnXvikMXgphrTRYzTKuB3tFg5iS.jpg' → 404 on
 *      https://certicars.pl/storage/... because production uses FILESYSTEM_DISK=s3
 *      and the file lives in R2 (asset('storage/'.path) is the wrong URL).
 *
 * Pinned fixes:
 *   A. SecurityHeaders emits media-src 'self' <r2-origin>.
 *   B. catalog/show.blade.php damage photos use $d->image_url / $dp->url accessors.
 *   C. syncRelations damage uploads use safeStore — no DB row on failed upload.
 */
class MediaCspDamagePhotosTest extends TestCase
{
    use RefreshDatabase;

    private Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
    }

    // ===================================================================
    // FIX A — CSP media-src present
    // ===================================================================

    public function test_csp_header_contains_media_src_directive(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp, 'public response must carry a Content-Security-Policy header');
        $this->assertStringContainsString('media-src', $csp,
            "CSP must include media-src — without it, <video> falls back to default-src 'self' and blocks R2 videos."
        );
    }

    public function test_csp_media_src_includes_configured_cdn_origin_when_set(): void
    {
        $cdn = 'https://pub-2a11010f67b344f8ac55a00924cb5219.r2.dev';
        config(['filesystems.disks.public.url' => $cdn]);

        $response = $this->get('/');
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("media-src 'self' $cdn", $csp,
            'media-src must include the configured R2 public origin so <video src="https://pub-…r2.dev/…"> loads.'
        );
    }

    public function test_csp_media_src_falls_back_to_self_only_when_cdn_unset(): void
    {
        config(['filesystems.disks.public.url' => null]);

        $response = $this->get('/');
        $csp = $response->headers->get('Content-Security-Policy');
        // Should at least permit 'self' even if there's no CDN configured.
        $this->assertMatchesRegularExpression("/media-src\s+'self'/", $csp);
    }

    public function test_admin_routes_remain_without_csp_to_keep_admin_tooling_unaffected(): void
    {
        // Admin CSP bypass is intentional (lots of inline scripts/charts).
        // Confirm we didn't accidentally start emitting CSP on admin routes.
        $response = $this->get('/admin/login');
        $response->assertOk();
        $this->assertEmpty(
            $response->headers->get('Content-Security-Policy') ?? '',
            'admin routes must not emit CSP — admin tooling needs inline scripts/charts.'
        );
    }

    // ===================================================================
    // FIX B — Public damage photo URLs via accessors, not asset('storage/...')
    // ===================================================================

    public function test_public_show_blade_uses_accessor_for_damage_photos_not_raw_storage_path(): void
    {
        $source = file_get_contents(base_path('resources/views/catalog/show.blade.php'));

        // Must NOT generate damage URLs via asset('storage/'.path) — broken on R2.
        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$d->image_path\s*\)/",
            $source,
            "Public page must use \$d->image_url accessor — asset('storage/...') 404s on R2 production."
        );
        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$dp->path\s*\)/",
            $source,
            "Public page must use \$dp->url accessor — asset('storage/...') 404s on R2 production."
        );

        // Must USE the accessors.
        $this->assertStringContainsString('$d->image_url', $source);
        $this->assertStringContainsString('$dp->url', $source);
    }

    public function test_damage_photo_url_accessor_returns_r2_origin_for_s3_disk(): void
    {
        // Verify the URL ACCESSOR directly (don't render the full page — that
        // requires a complete S3 config with region/key/secret which is
        // env-dependent). The accessor is what the public Blade now calls.
        // Set s3 driver + minimal config sufficient for url() generation.
        config([
            'filesystems.disks.public.driver' => 's3',
            'filesystems.disks.public.key'    => 'fake-key',
            'filesystems.disks.public.secret' => 'fake-secret',
            'filesystems.disks.public.region' => 'auto',
            'filesystems.disks.public.bucket' => 'fake-bucket',
            'filesystems.disks.public.url'    => 'https://pub-fake-r2.dev',
        ]);

        // Force re-resolution so the new config is picked up.
        \Illuminate\Support\Facades\Storage::forgetDisk('public');

        $car = Car::create([
            'brand_id' => $this->brand->id, 'model' => 'AccessorTest', 'status' => 'active',
        ]);
        $img = CarImage::create([
            'car_id'    => $car->id,
            'type'      => 'damage',
            'path'      => 'cars/' . $car->id . '/damages/M533test.jpg',
        ]);

        $url = $img->url;
        $this->assertStringContainsString('pub-fake-r2.dev', $url,
            "CarImage::url accessor on s3 disk must use the configured R2 public URL. Got: $url"
        );
        $this->assertStringContainsString('M533test.jpg', $url);
        // It must NOT be the asset('storage/...') pattern.
        $this->assertDoesNotMatchRegularExpression(
            '#/storage/cars/' . $car->id . '/damages/M533test\.jpg$#',
            $url,
            "CarImage::url accessor on s3 disk must not generate a /storage/... URL."
        );
    }

    public function test_damage_image_url_accessor_returns_placeholder_on_missing_path(): void
    {
        $car = Car::create([
            'brand_id' => $this->brand->id, 'model' => 'PlaceholderTest', 'status' => 'active',
        ]);
        $img = CarImage::create([
            'car_id' => $car->id,
            'type'   => 'damage',
            'path'   => '',
        ]);
        $this->assertStringContainsString('placeholder', $img->url);
    }

    // ===================================================================
    // FIX C — Damage uploads use safeStore (no DB row on failed upload)
    // ===================================================================

    public function test_damage_single_image_upload_path_uses_safe_store(): void
    {
        // Source-grep regression guard.
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));

        // Damage SINGLE image upload must NOT call ->store() directly inside syncRelations.
        $this->assertDoesNotMatchRegularExpression(
            "/\\\$uploads\[\\\$index\]\['image'\]->store\(/",
            $source,
            "Damage single-image upload must use \$this->safeStore() to validate the store() return value."
        );

        // And must use safeStore in that block.
        $this->assertMatchesRegularExpression(
            "/\\\$this->safeStore\(\\\$uploads\[\\\$index\]\['image'\]/",
            $source,
            'Damage single-image upload must call $this->safeStore() so a failed R2 PUT does not write false to image_path.'
        );
    }

    public function test_damage_multi_image_upload_path_uses_safe_store_and_skips_on_failure(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));

        // Multi-photo upload must use safeStore.
        $this->assertMatchesRegularExpression(
            "/\\\$path\s*=\s*\\\$this->safeStore\(\\\$imgFile,/",
            $source,
            'Damage multi-image upload must use $this->safeStore().'
        );

        // And must SKIP CarImage::create when safeStore returns null. Allow
        // any whitespace/comments between the if-block and the continue;.
        // The CarImage::create call must NOT appear inside the null branch.
        $this->assertMatchesRegularExpression(
            '/\$path\s*=\s*\$this->safeStore\(\$imgFile,[^;]*;\s*if\s*\(\s*\$path\s*===\s*null\s*\)\s*\{[^}]*continue;\s*\}/s',
            $source,
            'Damage multi-image upload must skip CarImage::create with `continue` when safeStore returns null (no orphan DB rows).'
        );
    }
}
