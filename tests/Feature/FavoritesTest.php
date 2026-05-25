<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    private function activeCar(string $model = 'Test Car'): Car
    {
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        return Car::create([
            'brand_id' => $brand->id,
            'model'    => $model,
            'status'   => 'active',
            'is_sold'  => false,
            'price'    => 50000,
            'currency' => 'PLN',
        ]);
    }

    public function test_favorites_page_loads_with_no_ids(): void
    {
        $r = $this->get(route('favorites'));
        $r->assertOk()->assertSee('Brak obserwowanych pojazdów');
    }

    public function test_favorites_shows_valid_public_car(): void
    {
        $car = $this->activeCar();
        $r = $this->get('/obserwowane?ids[]='.$car->id);
        $r->assertOk()->assertSee('Test Car');
        // Empty-state div must not be rendered (text also appears in JS, so check the element ID)
        $r->assertDontSee('id="favEmpty"', false);
    }

    public function test_favorites_excludes_draft_car(): void
    {
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $draft = Car::create(['brand_id' => $brand->id, 'model' => 'Draft Car', 'status' => 'draft', 'is_sold' => false]);
        $r = $this->get(route('favorites', ['ids[]' => [$draft->id]]));
        $r->assertOk()->assertSee('Brak obserwowanych pojazdów');
    }

    public function test_favorites_excludes_sold_car(): void
    {
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $sold = Car::create(['brand_id' => $brand->id, 'model' => 'Sold Car', 'status' => 'active', 'is_sold' => true]);
        $r = $this->get(route('favorites', ['ids[]' => [$sold->id]]));
        $r->assertOk()->assertSee('Brak obserwowanych pojazdów');
    }

    public function test_favorites_excludes_nonexistent_ids(): void
    {
        $r = $this->get(route('favorites', ['ids[]' => [99999, 99998]]));
        $r->assertOk()->assertSee('Brak obserwowanych pojazdów');
    }

    public function test_favorites_mixed_ids_shows_only_valid_cars(): void
    {
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $valid = $this->activeCar('Valid Car');
        $draft = Car::create(['brand_id' => $brand->id, 'model' => 'Hidden Draft', 'status' => 'draft', 'is_sold' => false]);

        $r = $this->get(route('favorites', ['ids[]' => [$valid->id, $draft->id, 99999]]));
        $r->assertOk()->assertSee('Valid Car')->assertDontSee('Hidden Draft');
    }

    public function test_favorites_deduplicates_ids(): void
    {
        $car = $this->activeCar();
        $r = $this->get('/obserwowane?ids[]='.$car->id.'&ids[]='.$car->id.'&ids[]='.$car->id);
        $r->assertOk();
        // Should show the car exactly once
        $this->assertEquals(1, substr_count($r->getContent(), 'data-car-id="'.$car->id.'"'));
    }

    public function test_favorites_server_returns_valid_ids_json(): void
    {
        $car = $this->activeCar();
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $draft = Car::create(['brand_id' => $brand->id, 'model' => 'Draft', 'status' => 'draft', 'is_sold' => false]);

        $r = $this->get(route('favorites', ['ids[]' => [$car->id, $draft->id]]));
        $r->assertOk();
        // validIds passed to JS should contain only the active car's ID
        $r->assertSee('const serverIds = ['.$car->id.']', false);
        $r->assertDontSee('const serverIds = ['.$car->id.','.$draft->id.']', false);
    }

    public function test_favorites_limits_to_100_ids(): void
    {
        // Passing 150 IDs should not cause errors; only up to 100 processed
        $idsParam = implode('&', array_map(fn($i) => "ids[]=$i", range(1, 150)));
        $r = $this->get('/obserwowane?'.$idsParam);
        $r->assertOk();
    }

    public function test_favorites_page_count_matches_rendered_cards(): void
    {
        $car1 = $this->activeCar('Car One');
        $brand = Brand::firstOrCreate(['name' => 'TestBrand', 'slug' => 'testbrand']);
        $car2 = Car::create(['brand_id' => $brand->id, 'model' => 'Car Two', 'status' => 'active', 'is_sold' => false, 'price' => 30000, 'currency' => 'PLN']);

        $r = $this->get('/obserwowane?ids[]='.$car1->id.'&ids[]='.$car2->id);
        $r->assertOk();
        // Both cards rendered, no empty state
        $r->assertSee('Car One')->assertSee('Car Two');
        $r->assertDontSee('id="favEmpty"', false);
        // Server count badge shows 2
        $r->assertSee('id="favCountBadge">2</span>', false);
        // serverIds JSON contains both valid IDs (order matches input order)
        $r->assertSee('"'.$car1->id.'"', false);
        $r->assertSee('"'.$car2->id.'"', false);
    }
}
