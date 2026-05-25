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

/**
 * P1 SYSTEMATIC REPRODUCTION TEST
 *
 * Client report: save works with basic fields, fails when all fields are filled.
 * This test walks through phases A→N, adding one section at a time, then asserts:
 *  - the POST to admin.cars.store returns a redirect (= save succeeded)
 *  - the resulting car exists in DB
 *  - the redirect target (edit page) returns 200 (= render after save succeeded)
 *
 * Run with:
 *   php artisan test --filter=AdminFullFormReproTest
 */
class AdminFullFormReproTest extends TestCase
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
        Storage::fake('public');
    }

    // ---------- payload builders for each phase ----------

    private function payloadA_basic(): array
    {
        return [
            'brand_id' => $this->brand->id,
            'model'    => 'PhaseA',
            'status'   => 'active',
        ];
    }

    private function payloadB_vehicleData(): array
    {
        return [
            'first_registration' => '2020',
            'mileage'            => 100000,
            'color'              => 'Niebieski',
            'body_type'          => 'SUV',
            'fuel_type'          => 'Diesel',
            'transmission'       => 'Automatyczna',
            'transmission_detail'=> '8-biegowa',
            'power_hp'           => 190,
            'power_kw'           => 140,
            'engine_capacity'    => 1984,
            'doors'              => '5',
            'seats'              => 5,
            'vin'                => 'WAUZZZ8K7BA000001',
            'category'           => 'SUV',
        ];
    }

    private function payloadC_pricingSeller(): array
    {
        return [
            'price'           => 150000,
            'currency'        => 'PLN',
            'price_type'      => 'VAT marża',
            'taxation'        => 'Różnicowe opodatkowanie',
            'seller_name'     => 'Jan Sprzedawca',
            'seller_phone'    => '+48 123 456 789',
            'seller_email'    => 'sprzedawca@test.pl',
            'commission_note' => 'Bez prowizji',
        ];
    }

    private function payloadD_serviceHistory(): array
    {
        return [
            'previous_owners'       => '1',
            'country_registration'  => 'Niemcy',
            'imported_from'         => 'Niemcy',
            'is_imported'           => '1',
            'vehicle_history'       => 'Bezwypadkowy',
            'service_book'          => 'Tak',
            'service_book_status'   => 'Oryginalna',
            'registration_cert'     => 'Oryginał',
            'owners_manual'         => 'Tak',
            'vehicle_folder'        => 'Tak',
            'hu_au_report'          => 'Tak',
            'coc_documents'         => 'Kompletna',
            'aso_serviced'          => 'Tak',
            'service_history'       => 'Pełna historia',
            'last_service'          => '2024-10',
            'last_service_mileage'  => '95000',
            'next_inspection'       => '2025-10',
            'service_documentation' => 'Tak',
        ];
    }

    private function payloadE_fuelEmissions(): array
    {
        return [
            'fuel_consumption' => '6.5',
            'co2_emission'    => '160',
            'emission_class'  => 'Euro 6',
        ];
    }

    private function payloadF_equipment(): array
    {
        return [
            'equipment' => [
                'Komfort'      => "Klimatyzacja\nPodgrzewane fotele\nApple CarPlay",
                'Bezpieczeństwo' => "ABS\nESP\nPoduszki powietrzne",
                'Multimedia'   => "Nawigacja\nBluetooth",
            ],
        ];
    }

    private function payloadG_technicalConditions(): array
    {
        return [
            'technical_conditions' => [
                'engine'       => 'Sprawny, brak wycieków',
                'transmission' => 'Praca płynna',
                'suspension'   => 'Nowe amortyzatory',
                'electronics'  => 'Brak błędów DTC',
                'body'         => 'Lakier fabryczny',
                'brakes'       => 'Klocki 70%',
                'steering'     => 'Bez luzów',
                'exhaust'      => 'Szczelny',
                'ac'           => 'Sprawna',
            ],
        ];
    }

    private function payloadH_paintMeasurements(): array
    {
        return [
            'paint_measurements' => [
                ['area' => 'Maska', 'value' => 130],
                ['area' => 'Błotnik przedni lewy', 'value' => 145],
                ['area' => 'Błotnik przedni prawy', 'value' => 132],
                ['area' => 'Drzwi przednie lewe', 'value' => 128],
                ['area' => 'Drzwi przednie prawe', 'value' => 126],
                ['area' => 'Drzwi tylne lewe', 'value' => 130],
                ['area' => 'Drzwi tylne prawe', 'value' => 134],
                ['area' => 'Klapa bagażnika', 'value' => 140],
                ['area' => 'Dach', 'value' => 100],
                ['area' => 'Próg lewy', 'value' => 92],
                ['area' => 'Próg prawy', 'value' => 95],
                ['area' => 'Zderzak przedni', 'value' => 200],
                ['area' => 'Zderzak tylny', 'value' => 210],
            ],
        ];
    }

    private function payloadI_damagesNoPhotos(): array
    {
        return [
            'damages' => [
                [
                    'area'          => 'Zderzak przedni',
                    'severity'      => 'warning',
                    'type'          => 'damage',
                    'tags'          => 'rysa,otarcie',
                    'description'   => 'Niewielka rysa na zderzaku',
                    'position_x'    => '50',
                    'position_y'    => '30',
                    'position_view' => 'front',
                ],
                [
                    'area'          => 'Drzwi tylne prawe',
                    'severity'      => 'info',
                    'type'          => 'repaired',
                    'description'   => 'Polakierowany element',
                    'position_x'    => '70',
                    'position_y'    => '45',
                    'position_view' => 'right',
                ],
            ],
        ];
    }

    private function payloadJ_tireSets(): array
    {
        return [
            'tire_sets' => [
                [
                    'set_number' => '1',
                    'is_mounted' => '1',
                    'tire_type'  => 'summer',
                    'rim'        => '18"',
                    'notes'      => 'Mało jeżdżone',
                    'tires'      => [
                        ['position' => 'front_left',  'tread_depth' => '6.5', 'condition' => 'dobry'],
                        ['position' => 'front_right', 'tread_depth' => '6.4', 'condition' => 'dobry'],
                        ['position' => 'rear_left',   'tread_depth' => '6.8', 'condition' => 'dobry'],
                        ['position' => 'rear_right',  'tread_depth' => '6.7', 'condition' => 'dobry'],
                    ],
                ],
                [
                    'set_number' => '2',
                    'is_mounted' => '',
                    'tire_type'  => 'winter',
                    'rim'        => '17"',
                    'notes'      => '',
                    'tires'      => [
                        ['position' => 'front_left',  'tread_depth' => '5.0', 'condition' => 'dobry'],
                        ['position' => 'front_right', 'tread_depth' => '5.1', 'condition' => 'dobry'],
                    ],
                ],
            ],
        ];
    }

    private function payloadK_galleryImages(int $count = 3): array
    {
        $files = [];
        for ($i = 1; $i <= $count; $i++) {
            $files[] = UploadedFile::fake()->image("photo{$i}.jpg", 1200, 800);
        }
        return ['gallery_images' => $files];
    }

    private function payloadL_panoImages(): array
    {
        return [
            'pano360_image'    => UploadedFile::fake()->image('pano_in.jpg', 4096, 2048),
            'pano360ext_image' => UploadedFile::fake()->image('pano_out.jpg', 4096, 2048),
        ];
    }

    private function payloadM_engineVideo(): array
    {
        return ['engine_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'];
    }

    private function payloadN_full(): array
    {
        // Phase N = every section combined into a single save.
        return array_merge(
            $this->payloadA_basic(),
            $this->payloadB_vehicleData(),
            $this->payloadC_pricingSeller(),
            $this->payloadD_serviceHistory(),
            $this->payloadE_fuelEmissions(),
            $this->payloadF_equipment(),
            $this->payloadG_technicalConditions(),
            $this->payloadH_paintMeasurements(),
            $this->payloadI_damagesNoPhotos(),
            $this->payloadJ_tireSets(),
            $this->payloadK_galleryImages(3),
            $this->payloadL_panoImages(),
            $this->payloadM_engineVideo(),
            ['model' => 'PhaseN'], // unique marker
        );
    }

    // ---------- helper ----------

    /**
     * Submit a payload to admin.cars.store and verify:
     *   1. response status is a redirect (save succeeded server-side)
     *   2. car row exists with the expected model marker
     *   3. the redirect target (edit page) returns 200 (render after save works)
     */
    private function submitAndVerify(array $payload, string $expectedModel): Car
    {
        $store = $this->actingAs($this->admin)->post(route('admin.cars.store'), $payload);

        $errors = session('errors')?->all() ?? [];
        $flash  = session('error') ?? session('success') ?? session('warning');
        $this->assertSame(
            302,
            $store->getStatusCode(),
            "STORE was {$store->getStatusCode()}. Validation errors: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . " Flash: " . json_encode($flash, JSON_UNESCAPED_UNICODE)
        );

        $car = Car::where('model', $expectedModel)->first();
        $this->assertNotNull($car, "Car with model=$expectedModel NOT created. Errors: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . " Flash: " . json_encode($flash, JSON_UNESCAPED_UNICODE));

        $edit = $this->actingAs($this->admin)->get(route('admin.cars.edit', $car));
        $excerpt = $edit->getStatusCode() !== 200 ? mb_substr((string) $edit->getContent(), 0, 400) : '';
        $this->assertSame(200, $edit->getStatusCode(), "EDIT page returned {$edit->getStatusCode()} for car id={$car->id}. Body excerpt:\n$excerpt");

        return $car;
    }

    /**
     * Reload an existing car via PUT (the update path).
     */
    private function updateAndVerify(Car $car, array $payload): Car
    {
        $resp = $this->actingAs($this->admin)->put(route('admin.cars.update', $car), $payload);

        $errors = session('errors')?->all() ?? [];
        $flash  = session('error') ?? session('success') ?? session('warning');
        $this->assertSame(
            302,
            $resp->getStatusCode(),
            "UPDATE was {$resp->getStatusCode()}. Errors: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . " Flash: " . json_encode($flash, JSON_UNESCAPED_UNICODE)
        );

        $edit = $this->actingAs($this->admin)->get(route('admin.cars.edit', $car));
        $excerpt = $edit->getStatusCode() !== 200 ? mb_substr((string) $edit->getContent(), 0, 400) : '';
        $this->assertSame(200, $edit->getStatusCode(), "EDIT after UPDATE returned {$edit->getStatusCode()} for car id={$car->id}. Body excerpt:\n$excerpt");

        return $car->fresh();
    }

    // ---------- A through N ----------

    public function test_phase_A_basic_only(): void
    {
        $this->submitAndVerify(array_merge($this->payloadA_basic(), ['model' => 'PhaseA']), 'PhaseA');
    }

    public function test_phase_B_basic_plus_vehicle_data(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadB_vehicleData(), ['model' => 'PhaseB']);
        $this->submitAndVerify($p, 'PhaseB');
    }

    public function test_phase_C_basic_plus_pricing(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadC_pricingSeller(), ['model' => 'PhaseC']);
        $this->submitAndVerify($p, 'PhaseC');
    }

    public function test_phase_D_basic_plus_service_history(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadD_serviceHistory(), ['model' => 'PhaseD']);
        $this->submitAndVerify($p, 'PhaseD');
    }

    public function test_phase_E_basic_plus_fuel_emissions(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadE_fuelEmissions(), ['model' => 'PhaseE']);
        $this->submitAndVerify($p, 'PhaseE');
    }

    public function test_phase_F_basic_plus_equipment(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadF_equipment(), ['model' => 'PhaseF']);
        $this->submitAndVerify($p, 'PhaseF');
    }

    public function test_phase_G_basic_plus_technical_conditions(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadG_technicalConditions(), ['model' => 'PhaseG']);
        $this->submitAndVerify($p, 'PhaseG');
    }

    public function test_phase_H_basic_plus_paint_measurements(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadH_paintMeasurements(), ['model' => 'PhaseH']);
        $this->submitAndVerify($p, 'PhaseH');
    }

    public function test_phase_I_basic_plus_damages_no_photos(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadI_damagesNoPhotos(), ['model' => 'PhaseI']);
        $this->submitAndVerify($p, 'PhaseI');
    }

    public function test_phase_J_basic_plus_tire_sets(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadJ_tireSets(), ['model' => 'PhaseJ']);
        $this->submitAndVerify($p, 'PhaseJ');
    }

    public function test_phase_K_basic_plus_gallery_images(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadK_galleryImages(3), ['model' => 'PhaseK']);
        $this->submitAndVerify($p, 'PhaseK');
    }

    public function test_phase_L_basic_plus_pano(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadL_panoImages(), ['model' => 'PhaseL']);
        $this->submitAndVerify($p, 'PhaseL');
    }

    public function test_phase_M_basic_plus_engine_video_url(): void
    {
        $p = array_merge($this->payloadA_basic(), $this->payloadM_engineVideo(), ['model' => 'PhaseM']);
        $this->submitAndVerify($p, 'PhaseM');
    }

    public function test_phase_N_full_form_all_sections(): void
    {
        $p = $this->payloadN_full();
        $this->submitAndVerify($p, 'PhaseN');
    }

    // =========================================================================
    // EDGE CASES — beyond the basic A→N matrix
    // =========================================================================

    public function test_edge_resave_full_form_via_update(): void
    {
        // Client says "raz mi dodało, teraz wyjebało" → first save OK, second click broke.
        // Reproduce that exact flow: save a full car, then re-save (update) it.
        $car = $this->submitAndVerify($this->payloadN_full(), 'PhaseN');

        // Simulate the user clicking Zapisz a second time on the same (now existing) car.
        // The wizard form's edit page would re-submit *all* the fields including the
        // pre-existing relations + same paint/tech/equipment.
        $rePayload = array_merge(
            $this->payloadA_basic(),
            $this->payloadB_vehicleData(),
            $this->payloadC_pricingSeller(),
            $this->payloadD_serviceHistory(),
            $this->payloadE_fuelEmissions(),
            $this->payloadF_equipment(),
            $this->payloadG_technicalConditions(),
            $this->payloadH_paintMeasurements(),
            $this->payloadI_damagesNoPhotos(),
            $this->payloadJ_tireSets(),
            $this->payloadM_engineVideo(),
            ['model' => 'PhaseN'],
        );
        $this->updateAndVerify($car, $rePayload);
    }

    public function test_edge_previous_owners_textual_values(): void
    {
        foreach (['1', '2', '3+', 'Brak danych', '', null] as $val) {
            $p = array_merge($this->payloadA_basic(), ['model' => 'PrevOwn_' . ($val ?? 'NULL'), 'previous_owners' => $val]);
            $resp = $this->actingAs($this->admin)->post(route('admin.cars.store'), $p);
            $this->assertSame(302, $resp->getStatusCode(), "previous_owners=" . var_export($val, true) . " failed");
        }
    }

    public function test_edge_polish_chars_and_long_strings(): void
    {
        $p = array_merge(
            $this->payloadA_basic(),
            [
                'model'           => 'PolishChars',
                'color'           => 'Biały Pióropuszowy 🚗',
                'vehicle_history' => 'Bezwypadkowy — sprawdzony przez ASO',
                'commission_note' => str_repeat('ąęóśćźżĄĘÓŚĆŹŻ ', 50), // ~700 chars, well under max:1000
                'meta_description' => str_repeat('Ą', 300), // near max:320
            ]
        );
        $this->submitAndVerify($p, 'PolishChars');
    }

    public function test_edge_multiple_damages_with_array_tags(): void
    {
        $p = array_merge($this->payloadA_basic(), [
            'model'   => 'ManyDamages',
            'damages' => [
                ['area' => 'A', 'severity' => 'warning', 'type' => 'damage',  'tags' => 'r1,r2,r3', 'position_x' => '10', 'position_y' => '20', 'position_view' => 'top'],
                ['area' => 'B', 'severity' => 'info',    'type' => 'repaired','tags' => '',         'position_x' => '30', 'position_y' => '40', 'position_view' => 'front'],
                ['area' => 'C', 'severity' => 'danger',  'type' => 'accident','tags' => 'serious',  'position_x' => '50', 'position_y' => '60', 'position_view' => 'left'],
                ['area' => 'D', 'severity' => 'warning', 'type' => 'damage',  'description' => str_repeat('długi opis ', 100)],
            ],
        ]);
        $this->submitAndVerify($p, 'ManyDamages');
    }

    public function test_edge_paint_measurements_with_zero_and_string_values(): void
    {
        $p = array_merge($this->payloadA_basic(), [
            'model' => 'PaintEdges',
            'paint_measurements' => [
                ['area' => 'Maska', 'value' => '130'],     // value as string
                ['area' => 'Dach',  'value' => 0],           // zero (should skip in show)
                ['area' => 'Próg lewy', 'value' => ''],      // empty
                ['area' => 'Klapa', 'value' => '999'],       // way above threshold
            ],
        ]);
        $this->submitAndVerify($p, 'PaintEdges');
    }

    public function test_edge_equipment_with_empty_categories(): void
    {
        $p = array_merge($this->payloadA_basic(), [
            'model'     => 'EqEdges',
            'equipment' => [
                'Komfort'      => '',                       // empty string
                'Bezpieczeństwo' => "\n\n\n",                 // just whitespace
                'Multimedia'   => 'Single item',
            ],
        ]);
        $this->submitAndVerify($p, 'EqEdges');
    }

    public function test_edge_technical_conditions_empty_and_long(): void
    {
        $p = array_merge($this->payloadA_basic(), [
            'model' => 'TechEdges',
            'technical_conditions' => [
                'engine'       => '',
                'transmission' => null,
                'suspension'   => str_repeat('OK. ', 500),  // 2000 chars
                'electronics'  => 'Sprawne',
            ],
        ]);
        $this->submitAndVerify($p, 'TechEdges');
    }

    public function test_edge_full_form_then_full_update_then_full_update_again(): void
    {
        // Triple-save scenario: maybe the bug only happens on the 2nd or 3rd save.
        $car = $this->submitAndVerify($this->payloadN_full(), 'PhaseN');

        $payload = $this->payloadN_full();
        unset($payload['gallery_images'], $payload['pano360_image'], $payload['pano360ext_image']); // can't re-submit files
        $payload['model'] = 'PhaseN'; // keep same model

        $this->updateAndVerify($car, $payload);
        $this->updateAndVerify($car->fresh(), $payload); // again
        $this->updateAndVerify($car->fresh(), $payload); // and again
    }

    public function test_edge_update_with_only_some_relation_arrays_replaced(): void
    {
        // Save full, then update with damages array REMOVED → does damages stay in DB?
        // (User confusion if data disappears.)
        $car = $this->submitAndVerify($this->payloadN_full(), 'PhaseN');
        $this->assertGreaterThan(0, $car->damages()->count(), 'sanity: damages saved initially');
        $this->assertGreaterThan(0, $car->tireSets()->count(), 'sanity: tire sets saved initially');

        // Update without sending the damages key at all
        $payload = array_merge($this->payloadA_basic(), $this->payloadB_vehicleData(), ['model' => 'PhaseN']);
        $this->updateAndVerify($car, $payload);

        // Damages should be UNCHANGED (controller only touches relations when key is present)
        $this->assertGreaterThan(0, $car->fresh()->damages()->count(), 'damages must NOT be wiped when key is absent from payload');
        $this->assertGreaterThan(0, $car->fresh()->tireSets()->count(), 'tire sets must NOT be wiped when key is absent');
    }

    public function test_edge_update_with_empty_relation_arrays(): void
    {
        // What if the wizard sends damages=[] (empty array)? Does syncRelations()
        // wipe everything?
        $car = $this->submitAndVerify($this->payloadN_full(), 'PhaseN');
        $initialDamageCount = $car->damages()->count();
        $this->assertGreaterThan(0, $initialDamageCount);

        $payload = array_merge($this->payloadA_basic(), ['model' => 'PhaseN', 'damages' => []]);
        $this->updateAndVerify($car, $payload);

        // Document the behavior — this assertion will tell us what actually happens
        echo "\n[edge_empty_damages] before update: $initialDamageCount damages, after update with damages=[]: " . $car->fresh()->damages()->count() . " damages\n";
    }
}
