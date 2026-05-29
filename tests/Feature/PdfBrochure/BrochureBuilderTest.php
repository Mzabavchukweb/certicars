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
        // Vehicle fields now live in $vehicleData kv pairs. Dirty values
        // must drop out entirely; clean values appear as their own row.
        $labels = array_column($data->vehicleData, 'label');
        $this->assertNotContains('Kolor nadwozia', $labels);
        $upholstery = collect($data->vehicleData)->firstWhere('label', 'Kolor wnętrza / tapicerka');
        $this->assertSame('skóra', $upholstery['value']);
    }

    public function test_vehicle_data_includes_every_filled_field_in_correct_section(): void
    {
        $brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
        $car = Car::create([
            'brand_id'           => $brand->id,
            'model'              => 'A4',
            'status'             => 'active',
            'has_certicheck'     => true,
            'mileage'            => 105200,
            'fuel_type'          => 'petrol',
            'transmission'       => 'automatic',
            'transmission_detail' => 'S tronic 7-bieg.',
            'vin'                => 'WAUZZZ8K9JA567890',
            'body_type'          => 'sedan',
            'doors'              => 4,
            'seats'              => 5,
            'power_hp'           => 252,
            'engine_capacity'    => 1984,
            'first_registration' => '11/2018',
            'color'              => 'Florett Silver',
            'number_of_keys'     => 2,
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $labels = array_column($data->vehicleData, 'label');
        // Every filled field appears as a row in Dane pojazdu, in Polish.
        $this->assertContains('Marka', $labels);
        $this->assertContains('Model', $labels);
        $this->assertContains('Wersja', $labels);
        $this->assertContains('VIN', $labels);
        $this->assertContains('Typ nadwozia', $labels);
        $this->assertContains('Paliwo', $labels);
        $this->assertContains('Skrzynia biegów', $labels);
        $this->assertContains('Moc', $labels);
        $this->assertContains('Pojemność silnika', $labels);
        $this->assertContains('Liczba kluczyków', $labels);

        // Values are Polish, not raw enums.
        $fuel = collect($data->vehicleData)->firstWhere('label', 'Paliwo');
        $this->assertSame('Benzyna', $fuel['value']);
        $body = collect($data->vehicleData)->firstWhere('label', 'Typ nadwozia');
        $this->assertSame('Sedan', $body['value']);
    }

    public function test_empty_fields_do_not_create_blank_rows_in_any_section(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id'       => $brand->id,
            'model'          => 'Espace',
            'status'         => 'active',
            'has_certicheck' => true,
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        // Every kv row in every section has a non-empty value. No "—",
        // no null, no whitespace-only strings.
        foreach (['vehicleData','historyItems','documentItems','formalItems','serviceItems','fuelItems'] as $section) {
            foreach ($data->{$section} as $row) {
                $this->assertNotSame('', $row['value']);
                $this->assertNotSame('—', $row['value']);
                $this->assertMatchesRegularExpression('/\S/', $row['value']);
            }
        }
    }

    public function test_documents_and_formal_sections_render_canonical_certicars_lines(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id'         => $brand->id,
            'model'            => 'Espace',
            'status'           => 'active',
            'has_certicheck'   => true,
            'service_book_status' => 'complete',  // → "Kompletna"
            'owners_manual'    => 'tak',
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        // Documents canonical row "Faktura: VAT-marża" is always present.
        $faktura = collect($data->documentItems)->firstWhere('label', 'Faktura');
        $this->assertSame('VAT-marża', $faktura['value']);
        // Service book status enum mapped to Polish.
        $book = collect($data->documentItems)->firstWhere('label', 'Książka serwisowa');
        $this->assertSame('Kompletna', $book['value']);

        // Formalities canonical CertiCars-wide policy lines.
        $pcc = collect($data->formalItems)->firstWhere('label', 'PCC 2%');
        $this->assertSame('Kupujący zwolniony', $pcc['value']);
        $cost = collect($data->formalItems)->firstWhere('label', 'Koszt rejestracji');
        $this->assertSame('Po stronie kupującego', $cost['value']);
    }

    public function test_fuel_section_strips_embedded_units_and_normalises_decimal(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id'         => $brand->id,
            'model'            => 'Espace',
            'status'           => 'active',
            'has_certicheck'   => true,
            'fuel_consumption' => '7,2 l/100 km',   // admin typed units + comma decimal
            'emission_class'   => 'euro 6d',
            'co2_emission'     => '163',
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $avg = collect($data->fuelItems)->firstWhere('label', 'Średnie zużycie');
        $this->assertSame('7.2 l/100 km', $avg['value']);
        $emission = collect($data->fuelItems)->firstWhere('label', 'Norma emisji');
        $this->assertSame('Euro 6d', $emission['value']);
        $co2 = collect($data->fuelItems)->firstWhere('label', 'Emisja CO₂');
        $this->assertSame('163 g/km', $co2['value']);
    }

    public function test_damage_severity_is_mapped_to_polish_not_raw_enum(): void
    {
        $brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
        $car = Car::create([
            'brand_id' => $brand->id, 'model' => 'A4',
            'status' => 'active', 'has_certicheck' => true,
        ]);
        foreach (['low', 'medium', 'high', 'garbage'] as $sev) {
            CarDamage::create([
                'car_id'   => $car->id,
                'area'     => 'hood',
                'type'     => 'accident',
                'severity' => $sev,
            ]);
        }

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertSame('Lekkie',     $data->damages[0]['severity']);
        $this->assertSame('Umiarkowane',$data->damages[1]['severity']);
        $this->assertSame('Znaczące',   $data->damages[2]['severity']);
        // Unknown free-text severities drop to null instead of leaking raw.
        $this->assertNull($data->damages[3]['severity']);
    }

    public function test_contact_strip_renders_required_phone(): void
    {
        $brand = Brand::create(['name' => 'Renault', 'slug' => 'renault']);
        $car   = Car::create([
            'brand_id' => $brand->id, 'model' => 'Espace',
            'status' => 'active', 'has_certicheck' => true,
        ]);

        [$data] = $this->builder()->build($car, 'rid');

        $this->assertSame('+48 515 440 623',   $data->contactPhone);
        $this->assertSame('kontakt@certicars.pl', $data->contactEmail);
        $this->assertSame('certicars.pl',     $data->contactWebsite);
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
