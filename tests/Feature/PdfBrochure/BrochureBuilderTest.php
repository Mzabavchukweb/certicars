<?php

namespace Tests\Feature\PdfBrochure;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarDamage;
use App\Models\CarImage;
use App\Models\CarTire;
use App\Models\CarTireSet;
use App\PdfBrochure\BrochureBuilder;
use App\PdfBrochure\ImageEmbedder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Behaviour pins:
 *  - the DTO carries no raw enum keys
 *  - the DTO carries no sanitizer-blocked slang
 *  - sections without data are absent from the DTO (not just hidden in view)
 *  - duplicate damage photos are deduped
 *  - missing images don't crash; they just don't appear
 */
class BrochureBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function jpegBytes(): string
    {
        $im = imagecreatetruecolor(100, 60);
        imagefilledrectangle($im, 0, 0, 100, 60, imagecolorallocate($im, 200, 100, 80));
        ob_start();
        imagejpeg($im, null, 80);
        $bytes = ob_get_clean();
        imagedestroy($im);
        return $bytes;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function builder(): BrochureBuilder
    {
        return new BrochureBuilder(new ImageEmbedder('rid', null, false));
    }

    public function test_builds_a_basic_certified_car(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car = Car::create([
            'brand_id'           => $brand->id,
            'model'              => 'Espace',
            'price'              => 48900,
            'currency'           => 'PLN',
            'status'             => 'active',
            'is_sold'            => false,
            'has_certicheck'     => true,
            'mileage'            => 136000,
            'fuel_type'          => 'diesel',
            'transmission'       => 'automatic',
            'power_hp'           => 160,
            'first_registration' => '2015',
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertSame('Renault', $data->brand);
        $this->assertSame('Espace', $data->model);
        $this->assertSame('Diesel', $data->fuelType);
        $this->assertSame('Automatyczna', $data->transmission);
        $this->assertSame(136000, $data->mileage);
        $this->assertSame(160, $data->powerHp);
    }

    public function test_tire_set_blocks_slang_and_keeps_clean_metadata(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id' => $brand->id,
            'model' => 'Espace', 'status' => 'active', 'has_certicheck' => true,
        ]);
        $set = CarTireSet::create([
            'car_id'     => $car->id,
            'set_number' => 1,
            'tire_type'  => 'zajebiste opony 215/55 R17',  // slang
            'rim'        => 'Felgi aluminiowe 17"',          // clean
        ]);
        CarTire::create([
            'car_tire_set_id' => $set->id,
            'position'        => 'front_left',
            'tread_depth'     => 6.5,
            'condition'       => ['zajebiste'],
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertCount(1, $data->tireSets);
        $title = $data->tireSets[0]['title'];
        // tire_type was slang → dropped. rim was clean → kept.
        $this->assertStringNotContainsString('zajebiste',       $title);
        $this->assertStringContainsString('Felgi aluminiowe',   $title);
        $this->assertStringContainsString('Komplet 1',          $title);

        $tire = $data->tireSets[0]['tires'][0];
        $this->assertSame('Przednia lewa', $tire['position']);
        $this->assertSame('Wymaga uwagi',  $tire['label']);  // slang ['zajebiste'] never echoed
    }

    public function test_damage_card_with_no_valid_photo_yields_empty_photo_list(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id' => $brand->id, 'model' => 'Espace',
            'status' => 'active', 'has_certicheck' => true,
        ]);
        CarDamage::create([
            'car_id'      => $car->id,
            'area'        => 'front_left',
            'type'        => 'accident',
            'severity'    => 'medium',
            'tags'        => ['rysa', 'lakier'],
            'description' => 'Lekka rysa biegnąca wzdłuż dolnej krawędzi.',
            'image_path'  => 'cars/1/nope.jpg',  // missing → embedder returns null
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertCount(1, $data->damages);
        $d = $data->damages[0];
        $this->assertSame('Przód lewy', $d['area']);       // enum mapped
        $this->assertSame('Wypadek',    $d['type']);
        $this->assertSame(['rysa', 'lakier'], $d['tags']); // both clean
        $this->assertSame([],           $d['photos']);     // none embedded
    }

    public function test_section_with_no_data_collapses_to_empty_array(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id' => $brand->id, 'model' => 'Espace',
            'status' => 'active', 'has_certicheck' => true,
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertSame([], $data->tireSets);
        $this->assertSame([], $data->damages);
        $this->assertSame([], $data->equipment);
        $this->assertSame([], $data->galleryImages);
        $this->assertSame([], $data->damageImages);
        $this->assertSame([], $data->paintMeasurements);
        $this->assertSame([], $data->technicalConditions);
        $this->assertNull($data->heroImage);
    }

    public function test_color_and_upholstery_get_scrubbed(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id'       => $brand->id,
            'model'          => 'Espace',
            'status'         => 'active',
            'has_certicheck' => true,
            'color'          => 'zajebiście szary',  // dirty
            'upholstery'     => 'skóra',             // clean
        ]);

        [$data] = $this->builder()->build($car, 'rid');
        $this->assertNull($data->color);
        $this->assertSame('skóra', $data->upholstery);
    }

    public function test_equipment_categories_drop_dirty_items_and_hide_empty_categories(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car = Car::create([
            'brand_id' => $brand->id, 'model' => 'Espace',
            'status' => 'active', 'has_certicheck' => true,
        ]);
        // Equipment field shape: ['category' => ['item', 'item', ...]].
        $car->update([
            'equipment' => [
                'safety'  => ['ABS', 'ESP', 'zajebiste poduszki', 'Asystent pasa ruchu'],
                'comfort' => ['zajebiste', 'kurwa testowe'],  // entire category should disappear
                'extras'  => ['Hak holowniczy'],
            ],
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $titles = array_column($data->equipment, 'title');
        $this->assertContains('Bezpieczeństwo', $titles);
        $this->assertNotContains('Komfort', $titles);  // collapsed
        $safety = array_values(array_filter($data->equipment, fn ($c) => $c['title'] === 'Bezpieczeństwo'))[0];
        $this->assertSame(['ABS', 'ESP', 'Asystent pasa ruchu'], $safety['items']);
    }

    public function test_hero_image_is_embedded_when_present(): void
    {
        Storage::disk('public')->put('cars/1/hero.jpg', $this->jpegBytes());
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id' => $brand->id, 'model' => 'Espace',
            'status' => 'active', 'has_certicheck' => true,
        ]);
        CarImage::create([
            'car_id'     => $car->id,
            'path'       => 'cars/1/hero.jpg',
            'type'       => 'gallery',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertNotNull($data->heroImage);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $data->heroImage->dataUri);
    }
}
