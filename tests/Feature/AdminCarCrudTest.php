<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCarCrudTest extends TestCase
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

    public function test_admin_can_create_car(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4 Quattro',
            'price'    => 50000,
            'status'   => 'active',
        ])->assertRedirect();

        $this->assertDatabaseHas('cars', ['model' => 'A4 Quattro']);
    }

    public function test_admin_can_update_car(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'draft']);
        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4 Updated',
            'status'   => 'active',
        ])->assertRedirect();

        $this->assertEquals('A4 Updated', $car->fresh()->model);
    }

    public function test_admin_can_delete_car(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'draft']);
        $this->actingAs($this->admin)->delete(route('admin.cars.destroy', $car))->assertRedirect();
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    public function test_is_sold_syncs_status_on_save(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'X', 'status' => 'active', 'is_sold' => false]);
        $car->update(['is_sold' => true]);
        $this->assertEquals('sold', $car->fresh()->status);
    }

    public function test_toggle_sold_flips_state(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Y', 'status' => 'active', 'is_sold' => false]);
        $this->actingAs($this->admin)
            ->patch(route('admin.cars.toggle-sold', $car))
            ->assertRedirect();
        $this->assertTrue($car->fresh()->is_sold);
        $this->assertEquals('sold', $car->fresh()->status);
    }

    public function test_upload_rejects_non_image(): void
    {
        Storage::fake('public');
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Z', 'status' => 'active']);
        $fake = UploadedFile::fake()->create('evil.php', 10, 'text/x-php');

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'Z',
            'status'   => 'active',
            'gallery_images' => [$fake],
        ])->assertSessionHasErrors('gallery_images.0');
    }

    public function test_upload_accepts_valid_image(): void
    {
        Storage::fake('public');
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Z', 'status' => 'active']);
        $img = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'Z',
            'status'   => 'active',
            'gallery_images' => [$img],
        ])->assertRedirect();

        $this->assertDatabaseCount('car_images', 1);
    }

    public function test_admin_bulk_sold(): void
    {
        $c1 = Car::create(['brand_id' => $this->brand->id, 'model' => 'A', 'status' => 'active']);
        $c2 = Car::create(['brand_id' => $this->brand->id, 'model' => 'B', 'status' => 'active']);

        $this->actingAs($this->admin)->post(route('admin.cars.bulk'), [
            'action' => 'sold',
            'ids'    => [$c1->id, $c2->id],
        ])->assertRedirect();

        $this->assertTrue($c1->fresh()->is_sold);
        $this->assertTrue($c2->fresh()->is_sold);
    }
}
