<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDamage;
use App\Models\CarImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * P1 regression suite — admin media upload + save reliability.
 *
 * Root cause confirmed by Antigravity:
 *   docker/php-uploads.ini had upload_max_filesize=25M, but Laravel/UI allowed
 *   100MB engine videos. PHP-FPM silently dropped the request → empty form,
 *   no car, no error. 30MB videos consistently failed.
 *
 * This suite locks in the six fixes:
 *   1. PHP upload_max_filesize raised to 120M (> Laravel's 100M ceiling)
 *   2. Polish validation messages for engine video size/MIME/upload
 *   3. Client-side video size guard in wizard (source check)
 *   4. handleEngineVideo uses safeStore + upload-first/delete-old-after
 *   5. Damage thumbnails use the R2-aware accessor (not asset(storage/...))
 *   6. Autosave FileList iteration via Array.from()
 */
class AdminMediaUploadSaveTest extends TestCase
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

    // ===================================================================
    // FIX 1 — PHP upload_max_filesize aligned with Laravel's 100M ceiling
    // ===================================================================

    public function test_php_upload_ini_allows_more_than_laravel_engine_video_max(): void
    {
        // The P1 regression: PHP rejected 30MB videos because upload_max_filesize
        // was 25M while Laravel allowed up to 100M (max:102400 KB).
        $dockerIni = file_get_contents(base_path('docker/php-uploads.ini'));
        $userIni   = file_get_contents(base_path('public/.user.ini'));

        $extractM = function (string $contents, string $key): int {
            if (!preg_match('/^' . preg_quote($key, '/') . '\s*=\s*(\d+)\s*M/im', $contents, $m)) {
                return 0;
            }
            return (int) $m[1];
        };

        $dockerMax = $extractM($dockerIni, 'upload_max_filesize');
        $userMax   = $extractM($userIni, 'upload_max_filesize');

        $this->assertGreaterThanOrEqual(
            120,
            $dockerMax,
            "docker/php-uploads.ini upload_max_filesize is {$dockerMax}M — must be >=120M so 100MB Laravel-allowed videos aren't dropped by PHP-FPM before validation."
        );
        $this->assertGreaterThanOrEqual(
            120,
            $userMax,
            "public/.user.ini upload_max_filesize is {$userMax}M — must stay in sync with docker/php-uploads.ini at >=120M."
        );
    }

    public function test_php_post_max_size_remains_at_least_300m(): void
    {
        $contents = file_get_contents(base_path('docker/php-uploads.ini'));
        $this->assertMatchesRegularExpression(
            '/post_max_size\s*=\s*(?:[3-9]\d{2,}|[1-9]\d{3,})M/i',
            $contents,
            'post_max_size must remain >=300M to support combined video + gallery + damage uploads.'
        );
    }

    // ===================================================================
    // FIX 2 — Polish validation messages
    // ===================================================================

    public function test_oversized_engine_video_returns_polish_error_not_silent_drop(): void
    {
        // 101 MB file — just above the 100M Laravel ceiling. With PHP now
        // allowing 120M, this reaches Laravel and gets a Polish error.
        $oversized = UploadedFile::fake()->create('engine.mp4', 101 * 1024, 'video/mp4');

        $response = $this->actingAs($this->admin)
            ->from(route('admin.cars.create'))
            ->post(route('admin.cars.store'), [
                'brand_id' => $this->brand->id,
                'model'    => 'OversizedVideoTest',
                'status'   => 'active',
                'engine_video_file' => $oversized,
            ]);

        $response->assertSessionHasErrors('engine_video_file');
        $errorMsg = session('errors')->first('engine_video_file');
        $this->assertStringContainsString('100 MB', $errorMsg, "Expected Polish 100 MB error, got: $errorMsg");
        $this->assertStringContainsString('Nagranie pracy silnika', $errorMsg);

        $this->assertDatabaseMissing('cars', ['model' => 'OversizedVideoTest']);
    }

    public function test_validation_message_source_includes_engine_video_polish_messages(): void
    {
        // Source-grep guard: removing the Polish msg array reintroduces the
        // unhelpful default "validation.uploaded" / "validation.max.file" key.
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));
        $this->assertStringContainsString('Nagranie pracy silnika jest za duże', $source);
        $this->assertStringContainsString('Nieobsługiwany format wideo', $source);
        $this->assertStringContainsString('engine_video_file.max', $source);
        $this->assertStringContainsString('engine_video_file.uploaded', $source);
    }

    // ===================================================================
    // FIX 3 — Client-side pre-submit guard for engine video
    // ===================================================================

    public function test_wizard_form_contains_engine_video_size_guard_js(): void
    {
        $source = file_get_contents(base_path('resources/views/admin/cars/wizard-form.blade.php'));
        $this->assertStringContainsString('ENGINE_VIDEO_MAX_BYTES', $source);
        $this->assertStringContainsString('100 * 1024 * 1024', $source);
        $this->assertStringContainsString('engine_video_file', $source);
        $this->assertStringContainsString('Nagranie pracy silnika jest za duże', $source);
    }

    // ===================================================================
    // FIX 4 — handleEngineVideo uses safeStore + upload-first
    // ===================================================================

    public function test_engine_video_under_limit_saves_successfully(): void
    {
        Storage::fake('public');
        $video = UploadedFile::fake()->create('engine.mp4', 5 * 1024, 'video/mp4');

        $response = $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
            'model'    => 'ValidVideoTest',
            'status'   => 'active',
            'engine_video_file' => $video,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $car = Car::where('model', 'ValidVideoTest')->firstOrFail();
        $this->assertNotNull($car->engine_video_path);
        $this->assertNotSame('false', $car->engine_video_path);
        $this->assertNotSame('', $car->engine_video_path);
        Storage::disk('public')->assertExists($car->engine_video_path);
    }

    public function test_handle_engine_video_uses_safe_store_helper(): void
    {
        // Source-grep regression guard: pre-fix code called ->store() directly,
        // ignoring the return value. The fix uses safeStore() so a failed R2
        // PUT returns null instead of "false" leaking into engine_video_path.
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));
        $this->assertMatchesRegularExpression(
            '/private function handleEngineVideo.*?safeStore/s',
            $source,
            'handleEngineVideo must use safeStore() helper for upload-return validation.'
        );
        $this->assertMatchesRegularExpression(
            '/private function handleEngineVideo.*?_engine_video_upload_failed/s',
            $source,
            'handleEngineVideo must surface upload failures via the image-failure flash channel.'
        );
    }

    public function test_handle_engine_video_does_not_delete_old_before_new_uploads(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/Admin/CarController.php'));
        // The fix moves the delete AFTER the successful safeStore + DB update.
        // This regex confirms the order: safeStore() is called, return value
        // is checked, AND only then is the old path deleted.
        $this->assertMatchesRegularExpression(
            '/private function handleEngineVideo.*?\$newPath\s*=\s*\$this->safeStore.*?\$car->update\(\[.engine_video_path.\s*=>\s*\$newPath\]\).*?Storage::disk\(.public.\)->delete\(\$oldPath\)/s',
            $source,
            'handleEngineVideo must upload-then-update-then-delete (not delete-before-upload).'
        );
    }

    // ===================================================================
    // FIX 5 — Damage thumbnails use accessor, not asset('storage/...')
    // ===================================================================

    public function test_wizard_form_uses_url_accessor_for_damage_thumbnails_not_raw_storage_path(): void
    {
        $source = file_get_contents(base_path('resources/views/admin/cars/wizard-form.blade.php'));

        // Must NOT use asset('storage/'.$dmg->image_path) — broken on R2
        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$dmg->image_path\s*\)/",
            $source,
            "Damage thumbnail must use \$dmg->image_url accessor — asset('storage/...') doesn't work with R2."
        );
        $this->assertDoesNotMatchRegularExpression(
            "/asset\(\s*'storage\/'\s*\.\s*\\\$dp->path\s*\)/",
            $source,
            "Damage photo thumbnail must use \$dp->url accessor — asset('storage/...') doesn't work with R2."
        );

        // Must USE the accessor
        $this->assertStringContainsString('$dmg->image_url', $source);
        $this->assertStringContainsString('$dp->url', $source);
    }

    public function test_damage_image_url_accessor_returns_correct_url_for_local_path(): void
    {
        $car = Car::create([
            'brand_id' => $this->brand->id,
            'model'    => 'DamageUrlTest',
            'status'   => 'active',
        ]);
        $damage = CarDamage::create([
            'car_id' => $car->id,
            'area'   => 'Maska',
            'image_path' => 'cars/' . $car->id . '/damages/test.jpg',
        ]);

        $url = $damage->image_url;
        $this->assertIsString($url);
        $this->assertStringContainsString('test.jpg', $url);
        // Must NOT be the raw /storage/... path with no protocol — it must be
        // a real URL (http/https) generated by the configured disk.
        $this->assertNotSame('/storage/' . $damage->image_path, $url);
    }

    public function test_damage_photo_url_accessor_returns_placeholder_when_path_missing(): void
    {
        $car = Car::create([
            'brand_id' => $this->brand->id,
            'model'    => 'DamagePhotoNullTest',
            'status'   => 'active',
        ]);
        $img = CarImage::create([
            'car_id'    => $car->id,
            'type'      => 'damage',
            'path'      => '',
        ]);

        $url = $img->url;
        $this->assertStringContainsString('placeholder', $url);
    }

    // ===================================================================
    // FIX 6 — Autosave FileList iteration via Array.from()
    // ===================================================================

    public function test_wizard_autosave_uses_array_from_for_filelist_iteration(): void
    {
        $source = file_get_contents(base_path('resources/views/admin/layouts/wizard.blade.php'));

        // Both spots that iterate file inputs MUST use Array.from(...).forEach.
        // Without it, calling .forEach directly on a FileList throws TypeError
        // on every submit/heartbeat → autosave silently broken when files attached.
        $this->assertStringContainsString('Array.from(el.files || []).forEach', $source);
        $this->assertStringContainsString('Array.from(input.files || []).forEach', $source);

        // And NO unprotected file-list forEach should remain (negative lookbehind:
        // matches the dangerous pattern only when NOT preceded by `Array.from`).
        $this->assertDoesNotMatchRegularExpression(
            "/(?<!Array\.from)\(el\.files\s*\|\|\s*\[\]\)\.forEach/",
            $source,
            "wizSerializeForm still has unprotected (el.files || []).forEach — would crash on file submit."
        );
        $this->assertDoesNotMatchRegularExpression(
            "/(?<!Array\.from)\(input\.files\s*\|\|\s*\[\]\)\.forEach/",
            $source,
            "_diag builder still has unprotected (input.files || []).forEach — would crash on file submit."
        );
    }

    // ===================================================================
    // CROSS-FIX — full normal save flow still works (no regression)
    // ===================================================================

    public function test_full_save_with_video_under_limit_and_damage_photo_succeeds(): void
    {
        Storage::fake('public');
        $video = UploadedFile::fake()->create('engine.mp4', 5 * 1024, 'video/mp4');
        $damagePhoto = UploadedFile::fake()->image('damage.jpg', 1200, 800);

        $response = $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
            'model'    => 'FullMediaTest',
            'status'   => 'active',
            'engine_video_file' => $video,
            'damages' => [
                [
                    'area'        => 'Zderzak',
                    'severity'    => 'warning',
                    'type'        => 'damage',
                    'image'       => $damagePhoto,
                ],
            ],
        ]);

        $response->assertRedirect();
        $car = Car::where('model', 'FullMediaTest')->firstOrFail();
        $this->assertNotNull($car->engine_video_path);
        $this->assertSame(1, $car->damages()->count());
        $this->assertNotNull($car->damages()->first()->image_path);
    }
}
