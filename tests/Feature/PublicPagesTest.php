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
            'consent' => '1',
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

    // ---------------------------------------------------------------------
    // "Widok 360° pojazdu" cards section (single-car page)
    // ---------------------------------------------------------------------

    public function test_pano360_cards_section_hidden_when_no_360_assets(): void
    {
        $car = $this->activeCar();
        // No CarImage of type pano360 / pano360ext seeded.
        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        // Card markup (button.cs-pano360-card) must not render when neither
        // 360 asset exists. The CSS class + JS function live in the page
        // regardless (style block + script block), so we assert the actual
        // rendered button element + the section heading <h3>.
        $this->assertStringNotContainsString('<button type="button" class="cs-pano360-card"', $html,
            'No card button must render when neither pano360 nor pano360ext exists.');
        $this->assertStringNotContainsString('>Widok 360° pojazdu</h3>', $html);
    }

    public function test_pano360_cards_section_renders_both_when_both_assets_exist(): void
    {
        $car = $this->activeCar();
        \App\Models\CarImage::create(['car_id' => $car->id, 'type' => 'pano360',     'path' => 'https://cdn.example.test/cars/'.$car->id.'/pano-interior.jpg', 'sort_order' => 0]);
        \App\Models\CarImage::create(['car_id' => $car->id, 'type' => 'pano360ext',  'path' => 'https://cdn.example.test/cars/'.$car->id.'/pano-exterior.jpg', 'sort_order' => 0]);

        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('Widok 360° pojazdu',     $html);
        $this->assertStringContainsString('360° z zewnątrz',         $html);
        $this->assertStringContainsString('360° wnętrza',            $html);
        $this->assertStringContainsString('csOpenPano360(\'pano360ext\')',  $html);
        $this->assertStringContainsString('csOpenPano360(\'pano360\')',     $html);
        // Both card images must use the absolute https URL, never the /storage/... raw path.
        $this->assertStringContainsString('pano-interior.jpg', $html);
        $this->assertStringContainsString('pano-exterior.jpg', $html);
    }

    public function test_pano360_cards_section_renders_single_when_only_one_asset(): void
    {
        $car = $this->activeCar();
        \App\Models\CarImage::create(['car_id' => $car->id, 'type' => 'pano360ext', 'path' => 'https://cdn.example.test/cars/'.$car->id.'/pano-exterior.jpg', 'sort_order' => 0]);

        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('Widok 360° pojazdu',     $html);
        $this->assertStringContainsString('cs-pano360-row single',  $html, 'Grid must collapse to a single-column variant when only one 360 asset is present.');
        // The card-title <h4> is unique to the new section; the gallery tab
        // buttons up top carry the same text so we have to match the card
        // markup specifically here.
        $this->assertStringContainsString('<h4 class="cs-pano360-card-title">360° z zewnątrz</h4>', $html);
        $this->assertStringNotContainsString('<h4 class="cs-pano360-card-title">360° wnętrza</h4>', $html);
    }

    // ---------------------------------------------------------------------
    // 'Pomiary grubości lakieru' + 'Koła i opony' two-card row
    // ---------------------------------------------------------------------

    public function test_paint_and_tires_cards_hidden_when_no_data(): void
    {
        $car = $this->activeCar();
        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        // Without paint_measurements OR tireSets, the .cs-pt-row container must not
        // render (the section is fully optional). CSS class definition is in the
        // <style> block regardless — we look for the actual element opener.
        $this->assertStringNotContainsString('<div class="cs-pt-row">', $html);
        $this->assertStringNotContainsString('>Pomiary grubości lakieru</h3>', $html);
        $this->assertStringNotContainsString('>Koła i opony</h3>',              $html);
    }

    public function test_paint_card_renders_status_colours_from_real_measurements(): void
    {
        $car = $this->activeCar();
        $car->update([
            'paint_measurements' => [
                ['area' => 'Dach',  'value' => 120],   // ≤150 → ok
                ['area' => 'Maska', 'value' => 210],   // 151–250 → warn
                ['area' => 'Drzwi', 'value' => 320],   // >250 → bad
            ],
        ]);
        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('Pomiary grubości lakieru', $html);
        $this->assertStringContainsString('Pomiary wykonane profesjonalnym czujnikiem', $html);

        // Each cell carries the threshold-driven CSS class.
        $this->assertMatchesRegularExpression(
            '/<div class="cs-pt-paint-cell ok">[\s\S]+?Dach[\s\S]+?120/',
            $html,
            'Dach 120µm must render as cs-pt-paint-cell.ok'
        );
        $this->assertMatchesRegularExpression(
            '/<div class="cs-pt-paint-cell warn">[\s\S]+?Maska[\s\S]+?210/',
            $html,
            'Maska 210µm must render as cs-pt-paint-cell.warn'
        );
        $this->assertMatchesRegularExpression(
            '/<div class="cs-pt-paint-cell bad">[\s\S]+?Drzwi[\s\S]+?320/',
            $html,
            'Drzwi 320µm must render as cs-pt-paint-cell.bad'
        );
        // Legend renders all three states.
        $this->assertStringContainsString('Fabryczna powłoka (≤150 µm)',  $html);
        $this->assertStringContainsString('Druga warstwa (151–250 µm)',   $html);
        $this->assertStringContainsString('Szpachla / naprawa (>250 µm)', $html);
    }

    public function test_paint_card_empty_when_only_zero_measurements(): void
    {
        // Measurements that all resolve to 0 (or empty) should fall to the empty
        // state, not show an empty grid.
        $car = $this->activeCar();
        $car->update([
            'paint_measurements' => [['area' => 'Dach', 'value' => 0]],
            'tireSets'           => [], // and tires absent so the whole row hides
        ]);
        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        // Row hidden entirely (no paint AND no tires).
        $this->assertStringNotContainsString('<div class="cs-pt-row">', $html);
    }

    public function test_tires_card_renders_reference_layout_from_real_tire_set(): void
    {
        $car = $this->activeCar();
        $set = \App\Models\CarTireSet::create([
            'car_id'     => $car->id,
            'set_number' => 1,
            'is_mounted' => true,
            'tire_type'  => 'Opony całoroczne',
            'rim'        => '16" Aluminium',
        ]);
        foreach ([
            'front_left'  => 6.5,
            'front_right' => 6.0,
            'rear_left'   => 5.5,
            'rear_right'  => 5.5,
        ] as $pos => $depth) {
            \App\Models\CarTire::create([
                'car_tire_set_id' => $set->id,
                'position'        => $pos,
                'tread_depth'     => $depth,
            ]);
        }

        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        // Section title + canonical layout markers.
        $this->assertStringContainsString('Koła i opony',           $html);
        $this->assertStringContainsString('cs-pt-tire-set',         $html);
        $this->assertStringContainsString('cs-pt-tire-table',       $html);
        $this->assertStringContainsString('cs-pt-tire-divider',     $html);
        // Set head: "1. Komplet (zamontowane)".
        $this->assertStringContainsString('Komplet',                $html);
        $this->assertStringContainsString('(zamontowane)',          $html);
        // Left metadata column.
        $this->assertStringContainsString('>Rodzaj opon<',          $html);
        $this->assertStringContainsString('Opony całoroczne',       $html);
        $this->assertStringContainsString('>Felga<',                $html);
        $this->assertStringContainsString('16&quot; Aluminium',     $html);
        // Four position labels (reference uses "Przednia lewa"/"Tylna prawa" etc.).
        $this->assertStringContainsString('Przednia lewa',          $html);
        $this->assertStringContainsString('Przednia prawa',         $html);
        $this->assertStringContainsString('Tylna lewa',             $html);
        $this->assertStringContainsString('Tylna prawa',            $html);
        // Tread depth row with values aligned to the four columns.
        $this->assertStringContainsString('Głębokość bieżnika',     $html);
        $this->assertStringContainsString('6.5 mm',                 $html);
        $this->assertStringContainsString('5.5 mm',                 $html);
        // Status row "Stan" with all-OK statuses.
        $this->assertStringContainsString('>Stan<',                 $html);
        $this->assertStringContainsString('Brak nieprawidłowości',  $html);
    }

    public function test_tires_card_marks_position_when_condition_has_issue(): void
    {
        $car = $this->activeCar();
        $set = \App\Models\CarTireSet::create([
            'car_id' => $car->id, 'set_number' => 1, 'is_mounted' => true, 'tire_type' => '205/55 R16',
        ]);
        \App\Models\CarTire::create([
            'car_tire_set_id' => $set->id, 'position' => 'front_left',  'tread_depth' => 6.0,
        ]);
        \App\Models\CarTire::create([
            'car_tire_set_id' => $set->id, 'position' => 'rear_right',  'tread_depth' => 3.0,
            'condition' => ['nierówny bieżnik'],
        ]);

        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        // Bad-positioned tire renders inside the status row with .warn class +
        // surfaces the condition text instead of "Brak nieprawidłowości".
        $this->assertMatchesRegularExpression('/cs-pt-tire-status-val warn[\s\S]+?nierówny bieżnik/', $html);
        // The OK tire keeps the green "Brak nieprawidłowości" status.
        $this->assertStringContainsString('Brak nieprawidłowości', $html);
    }

    // ---------------------------------------------------------------------
    // 'Podobne pojazdy' section + legal disclaimer bar
    // ---------------------------------------------------------------------

    public function test_related_cars_section_renders_with_brand_icon_and_subtitle(): void
    {
        // Create the current car + at least one related candidate of the same brand.
        $brand = Brand::create(['name' => 'BMW', 'slug' => 'bmw']);
        $current = Car::create([
            'brand_id' => $brand->id, 'model' => 'X5 Current', 'slug' => 'bmw-x5-current',
            'status' => 'active', 'is_sold' => false, 'price' => 80000, 'fuel_type' => 'Diesel',
        ]);
        Car::create([
            'brand_id' => $brand->id, 'model' => 'X3 Related', 'slug' => 'bmw-x3-related',
            'status' => 'active', 'is_sold' => false, 'price' => 70000, 'fuel_type' => 'Benzyna',
        ]);

        $html = $this->get('/samochody/'.$current->slug)->assertOk()->getContent();

        $this->assertStringContainsString('cs-related-section',            $html);
        $this->assertStringContainsString('cs-related-head-ico',           $html);
        $this->assertStringContainsString('>Podobne pojazdy</h3>',         $html);
        $this->assertStringContainsString('Inne samochody, które mogą Cię zainteresować.', $html);
        // Related car appears, current car is not in its own related strip.
        $this->assertStringContainsString('X3 Related', $html);
        // Fuel badge on the related card.
        $this->assertStringContainsString('vcard-fuel-badge', $html);
        // CTA wording on the card was updated to "Zobacz szczegóły".
        $this->assertStringContainsString('Zobacz szczegóły', $html);
    }

    public function test_related_cars_section_excludes_current_car(): void
    {
        $brand = Brand::create(['name' => 'Volvo', 'slug' => 'volvo']);
        $current = Car::create([
            'brand_id' => $brand->id, 'model' => 'XC60 Current', 'slug' => 'volvo-xc60-current',
            'status' => 'active', 'is_sold' => false, 'price' => 90000,
        ]);
        Car::create([
            'brand_id' => $brand->id, 'model' => 'V60 Sibling', 'slug' => 'volvo-v60-sibling',
            'status' => 'active', 'is_sold' => false, 'price' => 70000,
        ]);

        $html = $this->get('/samochody/'.$current->slug)->assertOk()->getContent();

        // The current car must never appear in its own Podobne pojazdy strip.
        $this->assertStringNotContainsString(
            '<a href="' . route('catalog.show', $current) . '" class="vcard">',
            $html,
            'Current car must be excluded from the related-cars strip.'
        );
        $this->assertStringContainsString('V60 Sibling', $html);
    }

    public function test_legal_disclaimer_bar_renders_below_related_cars(): void
    {
        $car = $this->activeCar();
        $html = $this->get('/samochody/'.$car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('cs-legal-bar', $html);
        $this->assertStringContainsString('Nie stanowią jednak oferty handlowej w rozumieniu art. 66 §1 Kodeksu cywilnego.', $html);
    }

    public function test_pano360_card_image_urls_never_use_raw_storage_path(): void
    {
        // Source-grep regression guard mirroring the PR #7 pattern: card images
        // must consume the R2-aware CarImage::url accessor, not asset('storage/'.path).
        $source = file_get_contents(base_path('resources/views/catalog/show.blade.php'));

        $this->assertDoesNotMatchRegularExpression(
            "/cs-pano360-card-img.*asset\(\s*'storage\/'/",
            $source,
            "360 card backgrounds must use \$pano->url, never asset('storage/...')."
        );
        $this->assertStringContainsString('$car->exteriorPano360Image->url', $source);
        $this->assertStringContainsString('$car->pano360Image->url',         $source);
    }
}
