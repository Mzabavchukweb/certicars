<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OtomotoImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.pl',
            'password' => Hash::make('secret'), 'is_admin' => true,
        ]);
    }

    public function test_rejects_non_otomoto_url(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), ['url' => 'https://evil.com/car/123'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('url');
    }

    public function test_rejects_missing_url(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('url');
    }

    public function test_rejects_file_scheme_ssrf(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), ['url' => 'file:///etc/passwd'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('url');
    }

    public function test_rejects_internal_ip_url(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), ['url' => 'http://169.254.169.254/latest/meta-data/'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('url');
    }

    public function test_handles_otomoto_404_gracefully(): void
    {
        Http::fake(['https://www.otomoto.pl/*' => Http::response('', 404)]);

        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/test-123',
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_handles_otomoto_403_gracefully(): void
    {
        Http::fake(['https://www.otomoto.pl/*' => Http::response('', 403)]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/test-123',
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertStringContainsString('403', $response->json('message'));
    }

    public function test_exception_response_does_not_expose_internal_details(): void
    {
        Http::fake(function () {
            throw new \Exception('DB password=super_secret_123 at /var/www/app/vendor/...');
        });

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/test-123',
            ])
            ->assertStatus(500)
            ->assertJson(['success' => false]);

        $message = $response->json('message');
        $this->assertStringNotContainsString('password', $message);
        $this->assertStringNotContainsString('/var/www', $message);
        $this->assertStringNotContainsString('DB ', $message);
    }

    public function test_parses_next_data_and_returns_car_fields(): void
    {
        $nextData = [
            'props' => ['pageProps' => ['advert' => [
                'title'           => 'Audi A4 2.0 TDI',
                'price'           => ['amount' => '89000', 'currency' => 'PLN'],
                'description'     => '<p>Super auto, bezwypadkowe.</p>',
                'parametersDict'  => [
                    'make'            => ['values' => [['value' => 'audi',    'label' => 'Audi']]],
                    'model'           => ['values' => [['value' => 'a4',      'label' => 'A4']]],
                    'year'            => ['values' => [['value' => '2020',    'label' => '2020']]],
                    'mileage'         => ['values' => [['value' => '85000',   'label' => '85 000 km']]],
                    'engine_capacity' => ['values' => [['value' => '1968',    'label' => '1968 cm3']]],
                    'engine_power'    => ['values' => [['value' => '150',     'label' => '150 KM']]],
                    'fuel_type'       => ['values' => [['value' => 'diesel',  'label' => 'Diesel']]],
                    'gearbox'         => ['values' => [['value' => 'manual',  'label' => 'Manualna']]],
                    'body_type'       => ['values' => [['value' => 'sedan',   'label' => 'Sedan']]],
                    'color'           => ['values' => [['value' => 'czarny',  'label' => 'Czarny']]],
                ],
            ]]],
        ];

        $html = '<html><script id="__NEXT_DATA__" type="application/json">'
            . json_encode($nextData)
            . '</script></html>';

        Http::fake(['https://www.otomoto.pl/*' => Http::response($html, 200)]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/audi-a4',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertEquals('Audi', $data['brand']);
        $this->assertEquals('A4', $data['model']);
        $this->assertEquals(89000.0, $data['price']);
        $this->assertEquals(85000, $data['mileage']);
        $this->assertEquals(150, $data['power_hp']);
        $this->assertEquals(1968, $data['engine_capacity']);
        $this->assertEquals('Diesel', $data['fuel_type']);
        $this->assertEquals('Manualna', $data['transmission']);
        $this->assertEquals('Sedan', $data['body_type']);
        $this->assertEquals('Super auto, bezwypadkowe.', $data['description']);
    }

    public function test_production_year_fallback_sets_first_registration(): void
    {
        $nextData = [
            'props' => ['pageProps' => ['advert' => [
                'title' => 'BMW 3',
                'parametersDict' => [
                    'make'  => ['values' => [['value' => 'bmw', 'label' => 'BMW']]],
                    'model' => ['values' => [['value' => '3',   'label' => '3 Series']]],
                    'year'  => ['values' => [['value' => '2019', 'label' => '2019']]],
                ],
            ]]],
        ];

        $html = '<html><script id="__NEXT_DATA__" type="application/json">'
            . json_encode($nextData)
            . '</script></html>';

        Http::fake(['https://www.otomoto.pl/*' => Http::response($html, 200)]);

        $data = $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/bmw-3',
            ])
            ->assertOk()
            ->json('data');

        $this->assertEquals('2019', $data['first_registration']);
    }

    public function test_import_payload_passes_car_store_validation(): void
    {
        Brand::create(['name' => 'Audi', 'slug' => 'audi']);

        $nextData = [
            'props' => ['pageProps' => ['advert' => [
                'title' => 'Audi A4 TDI',
                'price' => ['amount' => '75000', 'currency' => 'PLN'],
                'parametersDict' => [
                    'make'  => ['values' => [['value' => 'audi', 'label' => 'Audi']]],
                    'model' => ['values' => [['value' => 'a4',   'label' => 'A4']]],
                ],
            ]]],
        ];

        $html = '<html><script id="__NEXT_DATA__" type="application/json">'
            . json_encode($nextData)
            . '</script></html>';

        Http::fake(['https://www.otomoto.pl/*' => Http::response($html, 200)]);

        $importData = $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/audi-a4',
            ])
            ->assertOk()
            ->json('data');

        // Simulate admin reviewing import data then submitting the car form
        $brand = Brand::where('name', $importData['brand'])->first();
        $this->assertNotNull($brand, 'Imported brand must resolve to a DB record');

        $this->actingAs($this->admin)
            ->post(route('admin.cars.store'), [
                'brand_id' => $brand->id,
                'model'    => $importData['model'],
                'price'    => $importData['price'] ?? null,
                'status'   => 'draft',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cars', [
            'model' => 'A4',
        ]);
    }

    public function test_empty_page_returns_error_not_exception(): void
    {
        Http::fake(['https://www.otomoto.pl/*' => Http::response('<html><body>Not found</body></html>', 200)]);

        $this->actingAs($this->admin)
            ->postJson(route('admin.otomoto.import'), [
                'url' => 'https://www.otomoto.pl/osobowe/oferta/not-found',
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }
}
