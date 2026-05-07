<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    private function activeCar(): Car
    {
        $brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
        $car = Car::create([
            'brand_id' => $brand->id,
            'model'    => 'A4 Test',
            'price'    => 50000,
            'currency' => 'PLN',
            'status'   => 'active',
            'is_sold'  => false,
            'mileage'  => 100000,
            'first_registration' => '2020',
        ]);
        CarImage::create([
            'car_id'     => $car->id,
            'path'       => 'https://example.com/car.jpg',
            'type'       => 'gallery',
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        return $car->fresh();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_catalog_page_loads(): void
    {
        $this->activeCar();
        $this->get('/samochody')->assertOk()->assertSee('A4 Test');
    }

    public function test_car_show_page_loads_for_active_car(): void
    {
        $car = $this->activeCar();
        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertSee('Audi A4 Test')
            ->assertSee('application/ld+json', false);
    }

    public function test_car_show_returns_404_for_draft_to_anonymous(): void
    {
        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $car = Car::create(['brand_id' => $brand->id, 'model' => 'Draft', 'status' => 'draft']);
        $this->get('/samochody/'.$car->slug)->assertNotFound();
    }

    public function test_contact_form_stores_message(): void
    {
        $response = $this->post('/kontakt', [
            'name'    => 'Jan Testowy',
            'email'   => 'jan@test.pl',
            'phone'   => '+48 123 456 789',
            'message' => 'Wiadomość testowa z formularza kontaktowego.',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'jan@test.pl']);
    }

    public function test_contact_form_rejects_empty(): void
    {
        $this->post('/kontakt', [])->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_honeypot_rejects_bot(): void
    {
        $this->post('/kontakt', [
            'name'    => 'Bot',
            'email'   => 'bot@spam.pl',
            'message' => 'Dłuższa treść wiadomości testowej od bota.',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');
        $this->assertDatabaseMissing('contact_messages', ['email' => 'bot@spam.pl']);
    }

    public function test_sitemap_xml(): void
    {
        $this->activeCar();
        $r = $this->get('/sitemap.xml');
        $r->assertOk();
        $this->assertStringContainsString('application/xml', $r->headers->get('Content-Type'));
        $r->assertSee('<urlset', false);
        $r->assertSee('/samochody', false);
    }

    public function test_custom_404_page(): void
    {
        $this->get('/absolutely-nonexistent-path')
            ->assertNotFound()
            ->assertSee('Nie znaleziono strony');
    }
}
