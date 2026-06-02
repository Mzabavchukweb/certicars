<?php

namespace Tests\Feature;

use App\Jobs\ExtractInteriorFramesJob;
use App\Models\Brand;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InteriorVideoUploadTest extends TestCase
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

    public function test_uploading_interior_video_marks_pending_and_dispatches_job(): void
    {
        Storage::fake('public');
        Bus::fake();
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);

        $video = UploadedFile::fake()->create('interior.mp4', 1024, 'video/mp4');

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4',
            'status'   => 'active',
            'interior_video_file' => $video,
        ])->assertRedirect();

        $fresh = $car->fresh();
        $this->assertNotNull($fresh->interior_video_path);
        $this->assertEquals('pending', $fresh->interior_frames_status);
        $this->assertEquals('cars/' . $car->id . '/interior_frames', $fresh->interior_frames_dir);
        Storage::disk('public')->assertExists($fresh->interior_video_path);

        Bus::assertDispatched(ExtractInteriorFramesJob::class, function ($job) use ($car) {
            return $job->carId === $car->id;
        });
    }

    public function test_uploading_non_video_is_rejected(): void
    {
        Storage::fake('public');
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);

        $bogus = UploadedFile::fake()->create('not-a-video.txt', 10, 'text/plain');

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4',
            'status'   => 'active',
            'interior_video_file' => $bogus,
        ])->assertSessionHasErrors('interior_video_file');

        $this->assertNull($car->fresh()->interior_video_path);
    }

    public function test_remove_interior_video_clears_columns_and_files(): void
    {
        Storage::fake('public');
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);

        $framesDir = 'cars/' . $car->id . '/interior_frames';
        Storage::disk('public')->put('cars/' . $car->id . '/interior_video/in.mp4', 'fake');
        Storage::disk('public')->put($framesDir . '/frame_001.jpg', 'fake');
        $car->forceFill([
            'interior_video_path'    => 'cars/' . $car->id . '/interior_video/in.mp4',
            'interior_frames_status' => 'ready',
            'interior_frames_count'  => 1,
            'interior_frames_dir'    => $framesDir,
        ])->save();

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4',
            'status'   => 'active',
            'remove_interior_video' => 1,
        ])->assertRedirect();

        $fresh = $car->fresh();
        $this->assertNull($fresh->interior_video_path);
        $this->assertNull($fresh->interior_frames_status);
        $this->assertNull($fresh->interior_frames_count);
        $this->assertNull($fresh->interior_frames_dir);
        Storage::disk('public')->assertMissing('cars/' . $car->id . '/interior_video/in.mp4');
        Storage::disk('public')->assertMissing($framesDir . '/frame_001.jpg');
    }

    public function test_has_interior_frames_helper_reflects_state(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);
        $this->assertFalse($car->hasInteriorFrames());

        $car->forceFill([
            'interior_frames_status' => 'processing',
            'interior_frames_count'  => 60,
            'interior_frames_dir'    => 'cars/1/interior_frames',
        ])->save();
        $this->assertFalse($car->fresh()->hasInteriorFrames(), 'processing status is not ready');

        $car->forceFill(['interior_frames_status' => 'ready'])->save();
        $this->assertTrue($car->fresh()->hasInteriorFrames());
        $this->assertCount(60, $car->fresh()->interiorFrameUrls());
    }
}
