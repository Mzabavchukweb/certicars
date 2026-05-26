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

    public function test_new_fields_displayed_on_car_page(): void
    {
        $car = $this->activeCar();
        $car->update([
            'imported_from'       => 'Niemcy',
            'vehicle_history'     => 'Bezwypadkowy',
            'aso_serviced'        => 'Tak',
            'service_history'     => 'Pełna historia',
            'service_book_status' => 'Oryginalna',
            'registration_cert'   => 'Oryginał',
            'owners_manual'       => 'Tak',
        ]);

        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertSee('Niemcy')
            ->assertSee('Bezwypadkowy')
            ->assertSee('Pełna historia');
    }

    public function test_certicheck_returns_404_for_car_without_certicheck(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => false]);

        $this->get('/samochody/'.$car->slug.'/certicheck')->assertNotFound();
    }

    public function test_certicheck_returns_404_for_inactive_car(): void
    {
        $brand = Brand::create(['name' => 'Ford', 'slug' => 'ford']);
        $car = Car::create([
            'brand_id'     => $brand->id,
            'model'        => 'Draft',
            'status'       => 'draft',
            'has_certicheck' => true,
        ]);

        $this->get('/samochody/'.$car->slug.'/certicheck')->assertNotFound();
    }

    public function test_certicheck_shows_new_service_fields_when_guard_fields_missing(): void
    {
        $car = $this->activeCar();
        $car->update([
            'has_certicheck'     => true,
            'service_book_status' => 'Oryginalna',
            'registration_cert'  => 'Oryginał',
            'owners_manual'      => 'Tak',
        ]);

        $this->get('/samochody/'.$car->slug.'/certicheck')
            ->assertOk()
            ->assertSee('Oryginalna')
            ->assertSee('Oryginał');
    }

    public function test_certicheck_shows_polish_technical_condition_labels(): void
    {
        $car = $this->activeCar();
        $car->update([
            'has_certicheck'       => true,
            'technical_conditions' => ['engine' => 'Sprawny', 'brakes' => 'Sprawny'],
        ]);

        $this->get('/samochody/'.$car->slug.'/certicheck')
            ->assertOk()
            ->assertSee('Silnik')
            ->assertSee('Hamulce')
            ->assertDontSee('engine')
            ->assertDontSee('brakes');
    }

    public function test_certicheck_shows_polish_equipment_category_labels(): void
    {
        $car = $this->activeCar();
        $car->update([
            'has_certicheck' => true,
            'equipment'      => ['safety' => ['ABS', 'ESP'], 'comfort' => ['Podgrzewane fotele']],
        ]);

        $this->get('/samochody/'.$car->slug.'/certicheck')
            ->assertOk()
            ->assertSee('Bezpieczeństwo')
            ->assertSee('Komfort')
            ->assertDontSee('>safety<', false)
            ->assertDontSee('>comfort<', false);
    }

    public function test_pdf_returns_404_for_car_without_certicheck(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => false]);

        $this->get('/samochody/'.$car->slug.'/pdf')->assertNotFound();
    }

    public function test_pdf_renders_for_car_with_certicheck(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => true]);

        $this->get('/samochody/'.$car->slug.'/pdf')->assertOk();
    }

    public function test_pdf_view_shows_polish_technical_condition_labels(): void
    {
        $car = $this->activeCar();
        $car->update([
            'has_certicheck'       => true,
            'technical_conditions' => ['transmission' => 'Sprawna', 'suspension' => 'Sprawne'],
        ]);
        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires');

        $html = view('pdf.brochure', compact('car'))->render();

        $this->assertStringContainsString('Skrzynia biegów', $html);
        $this->assertStringContainsString('Zawieszenie', $html);
        $this->assertStringNotContainsString('>transmission<', $html);
    }

    public function test_pdf_view_shows_new_service_fields_when_old_guard_fields_missing(): void
    {
        $car = $this->activeCar();
        $car->update([
            'has_certicheck'      => true,
            'service_book_status' => 'Oryginalna',
            'aso_serviced'        => 'Tak',
        ]);
        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires');

        $html = view('pdf.brochure', compact('car'))->render();

        $this->assertStringContainsString('Oryginalna', $html);
        $this->assertStringContainsString('Tak', $html);
    }

    public function test_equipment_items_shown_individually_not_as_joined_string(): void
    {
        $car = $this->activeCar();
        $car->update([
            'equipment' => ['safety' => ['ABS', 'ESP', 'Poduszki powietrzne']],
        ]);

        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertSee('ABS')
            ->assertSee('ESP')
            ->assertSee('Poduszki powietrzne');
    }

    public function test_car_detail_shows_certicheck_link_when_available(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => true]);

        // The promotional green "Sprawdzony CertiCheck / Raport / PDF" trust banner
        // was removed from the public car page by design — only the CertiCheck CTA
        // remains visible there. The PDF endpoint stays reachable directly.
        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertSee(route('catalog.certicheck', $car->slug), false);

        // PDF route remains functional even though it's no longer linked from the car page.
        $this->get(route('car.pdf', $car->slug))->assertOk();
    }

    public function test_car_detail_hides_certicheck_section_when_unavailable(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => false]);

        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertDontSee(route('catalog.certicheck', $car->slug), false);
    }

    public function test_car_detail_hides_equipment_section_when_empty(): void
    {
        $car = $this->activeCar();
        $car->update(['equipment' => null]);

        $this->get('/samochody/'.$car->slug)
            ->assertOk()
            ->assertDontSee('pozycji');
    }

    public function test_inquiry_route_stores_inquiry_and_returns_json(): void
    {
        $car = $this->activeCar();

        $this->postJson(route('inquiry.store'), [
            'car_id'  => $car->id,
            'type'    => 'general',
            'name'    => 'Jan Testowy',
            'phone'   => '+48 600 000 000',
            'website' => '',
        ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', ['car_id' => $car->id, 'name' => 'Jan Testowy']);
    }

    public function test_inquiry_route_returns_validation_errors_as_json(): void
    {
        $car = $this->activeCar();

        $this->postJson(route('inquiry.store'), [
            'car_id' => $car->id,
            'type'   => 'general',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    public function test_car_deletion_removes_related_images_from_db(): void
    {
        $car = $this->activeCar();
        $carId = $car->id;

        $this->assertDatabaseHas('car_images', ['car_id' => $carId]);

        $car->delete();

        $this->assertDatabaseMissing('car_images', ['car_id' => $carId]);
    }

    public function test_pdf_renders_without_gallery_images(): void
    {
        $brand = Brand::create(['name' => 'Skoda', 'slug' => 'skoda']);
        $car = Car::create([
            'brand_id'       => $brand->id,
            'model'          => 'Octavia NoImg',
            'price'          => 30000,
            'currency'       => 'PLN',
            'status'         => 'active',
            'is_sold'        => false,
            'mileage'        => 50000,
            'first_registration' => '2019',
            'has_certicheck' => true,
        ]);
        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires');

        $html = view('pdf.brochure', compact('car'))->render();

        $this->assertStringContainsString('Skoda', $html);
    }

    public function test_car_image_alt_falls_back_to_car_title(): void
    {
        $car = $this->activeCar();
        $image = $car->images->first();

        $this->assertStringContainsString('Audi', $image->alt);
        $this->assertStringContainsString('zdjęcie', $image->alt);
    }

    public function test_sitemap_excludes_draft_cars(): void
    {
        $brand = Brand::create(['name' => 'Seat', 'slug' => 'seat']);
        Car::create(['brand_id' => $brand->id, 'model' => 'Draft', 'status' => 'draft', 'is_sold' => false, 'noindex' => false]);

        $r = $this->get('/sitemap.xml')->assertOk();
        $r->assertDontSee('/samochody/seat-draft', false);
    }

    public function test_sitemap_excludes_sold_cars(): void
    {
        $brand = Brand::create(['name' => 'Fiat', 'slug' => 'fiat']);
        Car::create(['brand_id' => $brand->id, 'model' => 'Sold', 'status' => 'active', 'is_sold' => true, 'noindex' => false]);

        $r = $this->get('/sitemap.xml')->assertOk();
        $r->assertDontSee('/samochody/fiat-sold', false);
    }

    public function test_sitemap_excludes_noindex_cars(): void
    {
        $brand = Brand::create(['name' => 'Kia', 'slug' => 'kia']);
        Car::create(['brand_id' => $brand->id, 'model' => 'Hidden', 'status' => 'active', 'is_sold' => false, 'noindex' => true]);

        $r = $this->get('/sitemap.xml')->assertOk();
        $r->assertDontSee('/samochody/kia-hidden', false);
    }

    public function test_sitemap_includes_certicheck_only_for_certified_cars(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => true]);

        $brand2 = Brand::create(['name' => 'Lada', 'slug' => 'lada']);
        $plain = Car::create(['brand_id' => $brand2->id, 'model' => 'Niva', 'status' => 'active', 'is_sold' => false, 'noindex' => false, 'has_certicheck' => false]);

        $r = $this->get('/sitemap.xml')->assertOk();
        $r->assertSee('/samochody/'.$car->slug.'/certicheck', false);
        $r->assertDontSee('/samochody/'.$plain->slug.'/certicheck', false);
    }

    public function test_car_detail_has_json_ld_with_price_brand_model(): void
    {
        $car = $this->activeCar();
        $car->update(['price' => 75000, 'currency' => 'PLN']);

        $body = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('application/ld+json', $body);
        $this->assertStringContainsString('"Vehicle"', $body);
        $this->assertStringContainsString('Audi', $body);
        $this->assertStringContainsString('A4 Test', $body);
        $this->assertStringContainsString('75000', $body);
        $this->assertStringContainsString('PLN', $body);
    }

    public function test_car_detail_has_breadcrumb_json_ld(): void
    {
        $car = $this->activeCar();

        $body = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('"BreadcrumbList"', $body);
        $this->assertStringContainsString('Oferta samochodów', $body);
    }

    public function test_car_detail_has_og_image_when_image_exists(): void
    {
        $car = $this->activeCar();

        $body = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('og:image', $body);
        $this->assertStringContainsString('https://example.com/car.jpg', $body);
    }

    public function test_car_detail_has_canonical_link(): void
    {
        $car = $this->activeCar();

        $body = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('rel="canonical"', $body);
        $this->assertStringContainsString('/samochody/'.$car->slug, $body);
    }

    public function test_sold_car_has_noindex_meta(): void
    {
        $car = $this->activeCar();
        $car->update(['noindex' => true]);

        $body = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('noindex', $body);
    }

    public function test_certicheck_page_has_meta_description(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => true]);

        $body = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();

        $this->assertStringContainsString('name="description"', $body);
        $this->assertStringContainsString('CertiCheck', $body);
    }

    public function test_certicheck_page_has_breadcrumb_json_ld(): void
    {
        $car = $this->activeCar();
        $car->update(['has_certicheck' => true]);

        $body = $this->get('/samochody/'.$car->slug.'/certicheck')->assertOk()->getContent();

        $this->assertStringContainsString('"BreadcrumbList"', $body);
    }
}
