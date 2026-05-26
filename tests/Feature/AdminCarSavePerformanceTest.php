<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Save performance regression suite.
 *
 * Root cause (audit): optimizeImage() ran AFTER the R2 upload — it then did
 * GET (download) + GD (decode/resize/encode) + PUT (re-upload), three R2
 * round-trips per gallery + damage image. For 15-photo saves the round-trip
 * dominated the 3–4 minute wait.
 *
 * Fix: optimizeUploadedFileInPlace() runs BEFORE the upload, on the local
 * PHP temp file. Same final output, ~3× fewer R2 calls per image.
 *
 * These tests pin:
 *   - the refactor presence (source guards against regression)
 *   - phase timing logs emit car.save.phase entries with rid/duration_ms
 *   - the upload-in-progress overlay exists in the wizard layout
 */
class AdminCarSavePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.pl',
            'password' => Hash::make('secret123'), 'is_admin' => true,
        ]);
        $this->brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
    }

    public function test_handle_images_uses_pre_upload_optimizer_not_post_upload_round_trip(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));

        // Source guard: handleImages must call optimizeUploadedFileInPlace BEFORE safeStore,
        // and must NOT call optimizeImage($path,...) afterwards (the slow R2 round-trip path).
        // Gallery upload pattern.
        $this->assertMatchesRegularExpression(
            '/optimizeUploadedFileInPlace\(\$file,\s*1920\).*?\$path\s*=\s*\$this->safeStore\(\$file,\s*[^\)]*\/gallery/s',
            $source,
            'gallery_images upload must call optimizeUploadedFileInPlace() BEFORE safeStore() (pre-upload optimization).'
        );

        // Damage upload pattern.
        $this->assertMatchesRegularExpression(
            '/optimizeUploadedFileInPlace\(\$file,\s*1280\).*?\$path\s*=\s*\$this->safeStore\(\$file,\s*[^\)]*\/damage/s',
            $source,
            'damage_images upload must call optimizeUploadedFileInPlace() BEFORE safeStore() (pre-upload optimization).'
        );

        // And the post-upload optimizeImage call inside handleImages must be gone.
        $this->assertStringNotContainsString(
            "\$this->optimizeImage(\$path, 1920);",
            $source,
            'gallery_images must NOT call $this->optimizeImage($path, 1920) anymore — that path downloads from R2 and re-uploads.'
        );
        $this->assertStringNotContainsString(
            "\$this->optimizeImage(\$path, 1280);",
            $source,
            'damage_images must NOT call $this->optimizeImage($path, 1280) anymore — that path downloads from R2 and re-uploads.'
        );
    }

    public function test_ajax_upload_endpoint_also_uses_pre_upload_optimizer(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));

        // The AJAX uploadImage endpoint (called from wizard per-photo upload)
        // shares the same fast path so admins adding photos one-by-one don't
        // pay the R2 round-trip cost either.
        $this->assertMatchesRegularExpression(
            '/public function uploadImage.*?optimizeUploadedFileInPlace\(\$imageFile,.*?\$imageFile->store\(/s',
            $source,
            'uploadImage AJAX endpoint must call optimizeUploadedFileInPlace() BEFORE store().'
        );
    }

    public function test_handle_images_wires_each_upload_phase_into_time_phase(): void
    {
        // Source-grep guard: every upload section in handleImages must be
        // wrapped in $this->timePhase(...) with a stable phase name so
        // production can correlate slow phases via rid in car.save.phase.
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));

        foreach ([
            'gallery_upload',
            'damage_upload',
            'pano360_upload',
            'pano360ext_upload',
            'engine_video_upload',
        ] as $phase) {
            $this->assertMatchesRegularExpression(
                "/timePhase\(\s*\\\$rid\s*,\s*\\\$op\s*,\s*\\\$car->id\s*,\s*'$phase'/",
                $source,
                "Upload section '$phase' must be wrapped in \$this->timePhase('$phase') for production phase-duration observability."
            );
        }

        // The timePhase helper itself must log under 'car.save.phase' with rid + duration_ms.
        $this->assertMatchesRegularExpression(
            "/private function timePhase.*?Log::info\('car\.save\.phase'.*?'rid'.*?'duration_ms'/s",
            $source,
            'timePhase helper must log car.save.phase with rid + duration_ms context.'
        );
    }

    public function test_save_success_log_includes_total_ms(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));
        // Both store() and update() must record a total_ms in their success log.
        $this->assertMatchesRegularExpression(
            "/car\\.save\\.success.*?'op'\\s*=>\\s*'store'.*?'total_ms'/s",
            $source,
            'store() car.save.success log must include total_ms.'
        );
        $this->assertMatchesRegularExpression(
            "/car\\.save\\.success.*?'op'\\s*=>\\s*'update'.*?'total_ms'/s",
            $source,
            'update() car.save.success log must include total_ms.'
        );
    }

    public function test_wizard_layout_contains_upload_progress_overlay(): void
    {
        $source = file_get_contents(base_path('resources/views/admin/layouts/wizard.blade.php'));

        $this->assertStringContainsString('id="wizUploadOverlay"', $source,
            'wizard layout must include the upload-in-progress overlay element.'
        );
        $this->assertStringContainsString('Przesyłanie zdjęć i filmu', $source,
            'overlay must carry the Polish "uploading photos and video" message.'
        );
        $this->assertStringContainsString('Nie zamykaj tej strony', $source,
            'overlay must instruct the operator not to close the page mid-upload.'
        );

        // wizSafeSubmit must reveal the overlay when files are attached.
        $this->assertStringContainsString('wizUploadOverlay', $source);
        $this->assertMatchesRegularExpression(
            '/var\s+hasFiles\s*=\s*false.*?input\.files\.length\s*>\s*0.*?hasFiles\s*=\s*true.*?overlay\.style\.display\s*=\s*[\'"]flex[\'"]/s',
            $source,
            'wizSafeSubmit must show overlay when any file input has files attached.'
        );
    }

    public function test_save_without_files_remains_fast_path(): void
    {
        // No files attached → no phase logs are required (timePhase is
        // additive but adds zero overhead beyond a single microtime() pair
        // per called section). Save itself must complete quickly.
        $start = microtime(true);
        $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
            'model'    => 'NoFilesFastPath',
            'status'   => 'active',
        ])->assertRedirect();
        $duration = microtime(true) - $start;

        // 2-second ceiling is generous on CI; the actual save with no files
        // should land under 100ms locally. This is a smoke floor that
        // guards against accidental N+1 queries / unbounded loops.
        $this->assertLessThan(2.0, $duration,
            "Save without files took {$duration}s — investigate N+1 or unbounded work."
        );

        $this->assertDatabaseHas('cars', ['model' => 'NoFilesFastPath']);
    }
}
