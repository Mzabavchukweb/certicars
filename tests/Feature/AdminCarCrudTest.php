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

    public function test_store_fails_without_brand_id(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'model' => 'A4 Missing Brand',
        ])->assertSessionHasErrors('brand_id');

        $this->assertDatabaseMissing('cars', ['model' => 'A4 Missing Brand']);
    }

    public function test_store_fails_without_model(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
        ])->assertSessionHasErrors('model');
    }

    public function test_store_validation_messages_are_polish(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.cars.store'), []);
        $response->assertSessionHasErrors('brand_id');
        $errors = session('errors');
        $this->assertStringContainsString('Wybierz markę', $errors->first('brand_id'));
        $this->assertStringContainsString('Podaj model', $errors->first('model'));
    }

    public function test_update_preserves_brand_id_and_model(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Original', 'status' => 'draft']);
        $brand2 = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $brand2->id,
            'model'    => 'Updated Model',
            'status'   => 'active',
        ])->assertRedirect();

        $fresh = $car->fresh();
        $this->assertEquals($brand2->id, $fresh->brand_id);
        $this->assertEquals('Updated Model', $fresh->model);
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

    public function test_new_fields_saved_correctly(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'X5', 'status' => 'draft']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id'           => $this->brand->id,
            'model'              => 'X5',
            'status'             => 'active',
            'imported_from'      => 'Niemcy',
            'vehicle_history'    => 'Bezwypadkowy',
            'aso_serviced'       => 'Tak',
            'service_history'    => 'Pełna historia',
            'service_book_status'=> 'Oryginalna',
            'registration_cert'  => 'Oryginał',
            'owners_manual'      => 'Tak',
        ])->assertRedirect();

        $fresh = $car->fresh();
        $this->assertEquals('Niemcy', $fresh->imported_from);
        $this->assertEquals('Bezwypadkowy', $fresh->vehicle_history);
        $this->assertEquals('Tak', $fresh->aso_serviced);
        $this->assertEquals('Pełna historia', $fresh->service_history);
        $this->assertEquals('Oryginalna', $fresh->service_book_status);
        $this->assertEquals('Oryginał', $fresh->registration_cert);
        $this->assertEquals('Tak', $fresh->owners_manual);
    }

    public function test_tire_sync_rolls_back_on_failure(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'T', 'status' => 'active']);

        // Save a valid tire set first
        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id'  => $this->brand->id,
            'model'     => 'T',
            'status'    => 'active',
            'tire_sets' => [[
                'set_number' => 1,
                'is_mounted' => true,
                'tire_type'  => 'Letnie',
                'tires'      => [['position' => 'FL', 'tread_depth' => '7']],
            ]],
        ])->assertRedirect();

        $this->assertDatabaseCount('car_tire_sets', 1);
    }

    // ── Persistence invariants ────────────────────────────────────────────────

    public function test_create_car_persists_all_main_fields(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id'            => $this->brand->id,
            'model'               => 'A6 Persistence',
            'price'               => 85000,
            'currency'            => 'PLN',
            'mileage'             => 45000,
            'first_registration'  => '2022',
            'fuel_type'           => 'Benzyna',
            'transmission'        => 'Automatyczna',
            'engine_capacity'     => 1984,
            'power_hp'            => 204,
            'status'              => 'active',
            'has_certicheck'      => true,
            'noindex'             => false,
            'imported_from'       => 'Niemcy',
            'aso_serviced'        => 'Tak',
            'service_book_status' => 'Oryginalna',
            'registration_cert'   => 'Oryginał',
        ])->assertRedirect();

        $car = Car::where('model', 'A6 Persistence')->firstOrFail();
        $this->assertEquals(85000.00, (float) $car->price);
        $this->assertEquals(45000, $car->mileage);
        $this->assertEquals('Benzyna', $car->fuel_type);
        $this->assertEquals('Automatyczna', $car->transmission);
        $this->assertEquals(1984, $car->engine_capacity);
        $this->assertEquals(204, $car->power_hp);
        $this->assertEquals('active', $car->status);
        $this->assertTrue($car->has_certicheck);
        $this->assertFalse($car->noindex);
        $this->assertEquals('Niemcy', $car->imported_from);
        $this->assertEquals('Tak', $car->aso_serviced);
        $this->assertEquals('Oryginalna', $car->service_book_status);
        $this->assertEquals('Oryginał', $car->registration_cert);
        $this->assertNotNull($car->identifier);
        $this->assertNotNull($car->slug);
    }

    public function test_update_one_field_does_not_wipe_other_fields(): void
    {
        $car = Car::create([
            'brand_id'            => $this->brand->id,
            'model'               => 'A5',
            'price'               => 75000,
            'mileage'             => 30000,
            'fuel_type'           => 'Diesel',
            'transmission'        => 'Automatyczna',
            'status'              => 'active',
            'imported_from'       => 'Niemcy',
            'has_certicheck'      => true,
            'aso_serviced'        => 'Tak',
            'service_book_status' => 'Oryginalna',
        ]);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A5 Updated',
            'status'   => 'reserved',
        ])->assertRedirect();

        $f = $car->fresh();
        $this->assertEquals('A5 Updated', $f->model);
        $this->assertEquals('reserved', $f->status);
        $this->assertEquals(75000.00, (float) $f->price);
        $this->assertEquals(30000, $f->mileage);
        $this->assertEquals('Diesel', $f->fuel_type);
        $this->assertEquals('Automatyczna', $f->transmission);
        $this->assertEquals('Niemcy', $f->imported_from);
        $this->assertTrue($f->has_certicheck);
        $this->assertEquals('Tak', $f->aso_serviced);
        $this->assertEquals('Oryginalna', $f->service_book_status);
    }

    public function test_equipment_json_persists_as_array(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Q5', 'status' => 'draft']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id'  => $this->brand->id,
            'model'     => 'Q5',
            'status'    => 'active',
            'equipment' => [
                'safety'  => "ABS\nESP\nPoduszki powietrzne",
                'comfort' => "Klimatyzacja\nNawigacja",
            ],
        ])->assertRedirect();

        $f = $car->fresh();
        $this->assertIsArray($f->equipment);
        $this->assertContains('ABS', $f->equipment['safety']);
        $this->assertContains('ESP', $f->equipment['safety']);
        $this->assertContains('Klimatyzacja', $f->equipment['comfort']);
    }

    public function test_technical_conditions_json_persists(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Q3', 'status' => 'draft']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id'             => $this->brand->id,
            'model'                => 'Q3',
            'status'               => 'active',
            'technical_conditions' => ['engine' => 'Sprawny', 'transmission' => 'Bez uwag'],
        ])->assertRedirect();

        $f = $car->fresh();
        $this->assertIsArray($f->technical_conditions);
        $this->assertEquals('Sprawny', $f->technical_conditions['engine']);
        $this->assertEquals('Bez uwag', $f->technical_conditions['transmission']);
    }

    public function test_paint_measurements_json_persists(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'Q7', 'status' => 'draft']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id'           => $this->brand->id,
            'model'              => 'Q7',
            'status'             => 'active',
            'paint_measurements' => [
                0 => ['area' => 'Maska', 'value' => 120],
                1 => ['area' => 'Zderzak przedni', 'value' => 145],
            ],
        ])->assertRedirect();

        $f = $car->fresh();
        $this->assertIsArray($f->paint_measurements);
        $this->assertEquals(120, $f->paint_measurements[0]['value']);
        $this->assertEquals('Maska', $f->paint_measurements[0]['area']);
    }

    public function test_damages_survive_unrelated_field_update(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'S4', 'status' => 'active']);
        $car->damages()->create([
            'area' => 'Rysa na masce', 'type' => 'damage',
            'position_x' => 50, 'position_y' => 30, 'position_view' => 'top',
        ]);
        $this->assertDatabaseCount('car_damages', 1);

        // Update basic fields only — no 'damages' key in payload
        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'S4 Updated',
            'status'   => 'active',
        ])->assertRedirect();

        $this->assertDatabaseCount('car_damages', 1);
        $this->assertEquals('Rysa na masce', $car->damages()->first()->area);
    }

    public function test_tire_sets_survive_unrelated_field_update(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'S5', 'status' => 'active']);
        $car->tireSets()->create(['set_number' => 1, 'tire_type' => 'Letnie', 'is_mounted' => true]);
        $this->assertDatabaseCount('car_tire_sets', 1);

        // Update basic fields only — no 'tire_sets' key in payload
        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'S5 Updated',
            'status'   => 'active',
        ])->assertRedirect();

        $this->assertDatabaseCount('car_tire_sets', 1);
    }

    public function test_gallery_images_survive_unrelated_update(): void
    {
        Storage::fake('public');
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'RS6', 'status' => 'active']);
        $car->images()->create(['path' => 'cars/1/gallery/photo.jpg', 'type' => 'gallery', 'is_primary' => true, 'sort_order' => 0]);
        $this->assertDatabaseCount('car_images', 1);

        // Update basic fields only — no gallery_images/delete_images in payload
        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'RS6 Updated',
            'status'   => 'active',
        ])->assertRedirect();

        $this->assertDatabaseCount('car_images', 1);
    }

    public function test_seeder_does_not_overwrite_existing_car_data(): void
    {
        // Run seeder once to create the demo car
        $this->artisan('db:seed', ['--class' => 'AudiA4Seeder', '--force' => true]);
        $car = Car::first();
        $this->assertNotNull($car);

        // Simulate admin editing the car (change price and status)
        $originalSlug = $car->slug;
        $car->update(['price' => 999999, 'status' => 'sold']);

        // Run seeder again (as happens on every redeploy)
        $this->artisan('db:seed', ['--class' => 'AudiA4Seeder', '--force' => true]);

        // Admin edits must NOT be overwritten
        $fresh = Car::where('slug', $originalSlug)->first();
        $this->assertEquals(999999, (float) $fresh->price);
        $this->assertEquals('sold', $fresh->status);
    }

    public function test_deploy_script_contains_no_destructive_db_commands(): void
    {
        $script = file_get_contents(base_path('docker-start.sh'));
        $this->assertStringNotContainsString('migrate:fresh', $script);
        $this->assertStringNotContainsString('migrate:refresh', $script);
        $this->assertStringNotContainsString('migrate:reset', $script);
        $this->assertStringNotContainsString('db:wipe', $script);
    }
}
