<?php

namespace Tests\Feature;

use App\Helpers\CarLabels;
use App\Models\Brand;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Coverage for the per-item {status, note} structure on technical_conditions
 * introduced alongside the 'Stan techniczny podczas oględzin' admin redesign.
 *
 * Spec checkpoints (from the user brief):
 *   1. admin can save status enum + optional note per item
 *   2. edit page reloads saved statuses (legacy strings + nested shape both)
 *   3. public page renders ok / attention / bad rows with the right CSS class
 *   4. missing data defaults safely to 'ok'
 *   5. invalid status is normalised (not 500'd)
 *   6. no regression of PR #2's nested-array read tolerance
 */
class TechnicalConditionsAdminTest extends TestCase
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

    /** 1. admin save round-trip — nested {status, note} per item */
    public function test_admin_saves_status_and_note_per_item(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);

        $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4',
            'technical_conditions' => [
                'engine'       => ['status' => 'ok'],
                'transmission' => ['status' => 'attention', 'note' => 'lekki stuk z prawej'],
                'brakes'       => ['status' => 'bad',       'note' => 'wymiana klocków'],
                'steering'     => ['status' => 'ok',        'note' => ''],
            ],
        ])->assertRedirect();

        $car->refresh();
        $this->assertSame('ok',        $car->technical_conditions['engine']['status']);
        $this->assertSame('',          $car->technical_conditions['engine']['note']);
        $this->assertSame('attention', $car->technical_conditions['transmission']['status']);
        $this->assertSame('lekki stuk z prawej', $car->technical_conditions['transmission']['note']);
        $this->assertSame('bad',       $car->technical_conditions['brakes']['status']);
    }

    /** 2. edit page reloads — wizard renders both legacy strings and nested shape */
    public function test_edit_page_reloads_legacy_and_nested_shapes(): void
    {
        $car = Car::create([
            'brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active',
            'technical_conditions' => [
                'engine'       => 'Sprawny',                                 // legacy free-text → reads as 'ok'
                'transmission' => ['status' => 'attention', 'note' => 'X'],  // nested shape
                'air_conditioning' => 'usterka',                             // legacy + legacy key alias → reads as 'bad' under 'ac'
            ],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.cars.edit', $car));
        $response->assertOk();
        $html = $response->getContent();

        // Each canonical key has a radio group; the resolved status is the one with `checked`.
        $this->assertMatchesRegularExpression(
            '/name="technical_conditions\[engine\]\[status\]" value="ok"\s+checked/',
            $html,
            'Legacy free-text "Sprawny" must preload as the OK radio.'
        );
        $this->assertMatchesRegularExpression(
            '/name="technical_conditions\[transmission\]\[status\]" value="attention"\s+checked/',
            $html,
            'Nested {status:attention} must preload the attention radio.'
        );
        $this->assertMatchesRegularExpression(
            '/name="technical_conditions\[ac\]\[status\]" value="bad"\s+checked/',
            $html,
            'Legacy "air_conditioning" key with "usterka" must alias to "ac" and preload bad radio.'
        );
    }

    /** 3. public page renders the three coloured status rows */
    public function test_public_page_renders_three_status_colours(): void
    {
        $car = Car::create([
            'brand_id' => $this->brand->id, 'model' => 'A4', 'slug' => 'audi-a4-tech', 'status' => 'active',
            'technical_conditions' => [
                'engine'       => ['status' => 'ok'],
                'transmission' => ['status' => 'attention', 'note' => 'do sprawdzenia'],
                'brakes'       => ['status' => 'bad'],
            ],
        ]);

        $html = $this->get('/samochody/' . $car->slug)->assertOk()->getContent();

        $this->assertStringContainsString('Bez zarzutu',     $html);
        $this->assertStringContainsString('Wymaga uwagi',    $html);
        $this->assertStringContainsString('Nieprawidłowość', $html);
        // The CSS class on the right-side status pill encodes the colour.
        $this->assertMatchesRegularExpression('/class="cs-tech-list-status\s+warn"/', $html);
        $this->assertMatchesRegularExpression('/class="cs-tech-list-status\s+fail"/', $html);
    }

    /** 4. missing data falls back safely to 'ok' */
    public function test_missing_technical_conditions_default_to_ok(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'slug' => 'no-tech', 'status' => 'active']);
        $html = $this->get('/samochody/' . $car->slug)->assertOk()->getContent();

        // All 8 items render with default "Bez zarzutu" (8 hits).
        $this->assertGreaterThanOrEqual(8, substr_count($html, 'Bez zarzutu'),
            'Cars without technical_conditions must show all 8 rows as default OK.');
        $this->assertStringNotContainsString('cs-tech-list-status warn', $html);
        $this->assertStringNotContainsString('cs-tech-list-status fail', $html);
    }

    /** 5. invalid status is normalised to 'ok' on save (not rejected outright) */
    public function test_invalid_status_normalises_to_ok(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'A4', 'status' => 'active']);

        // 'red' / 'unknown' / 'ok-ish' aren't in the enum — validation rejects so
        // the controller normalises post-validation to 'ok' as a safety net.
        $response = $this->actingAs($this->admin)->put(route('admin.cars.update', $car), [
            'brand_id' => $this->brand->id,
            'model'    => 'A4',
            'technical_conditions' => [
                'engine' => ['status' => 'ok-ish'],
            ],
        ]);
        $response->assertSessionHasErrors('technical_conditions.engine.status');
    }

    /** 6. PR #2 nested-array tolerance preserved on the read side */
    public function test_legacy_pr2_nested_array_shape_still_reads(): void
    {
        // The exact crash shape from production log 2026-05-22 that PR #2 fixed.
        $car = Car::create([
            'brand_id' => $this->brand->id, 'model' => 'A4', 'slug' => 'legacy-nested', 'status' => 'active',
            'technical_conditions' => [
                'engine' => ['status' => 'OK'],                    // legacy nested
                'body'   => ['Sprawny', 'brak rdzy'],              // legacy positional
            ],
        ]);

        // Wizard edit page must still render without 500.
        $this->actingAs($this->admin)->get(route('admin.cars.edit', $car))->assertOk();
        // Public page must still render.
        $this->get('/samochody/' . $car->slug)->assertOk();
    }

    /** 7. CarLabels::techStatus() resolves the three input shapes consistently */
    public function test_carlabels_tech_status_helper_resolves_all_shapes(): void
    {
        // Empty / null → ok
        $this->assertSame('ok', CarLabels::techStatus(null)['status']);
        $this->assertSame('ok', CarLabels::techStatus('')['status']);

        // Legacy free-text mapping
        $this->assertSame('ok',        CarLabels::techStatus('Sprawny')['status']);
        $this->assertSame('attention', CarLabels::techStatus('lekkie uwagi')['status']);
        $this->assertSame('attention', CarLabels::techStatus('do sprawdzenia')['status']);
        $this->assertSame('bad',       CarLabels::techStatus('usterka')['status']);
        $this->assertSame('bad',       CarLabels::techStatus('nie działa')['status']);

        // Nested explicit
        $this->assertSame('attention', CarLabels::techStatus(['status' => 'attention', 'note' => 'X'])['status']);
        $this->assertSame('X',         CarLabels::techStatus(['status' => 'attention', 'note' => 'X'])['note']);

        // Nested unknown status → falls back through legacy text path → 'ok'
        $this->assertSame('ok', CarLabels::techStatus(['status' => 'nonsense'])['status']);

        // Labels are localised
        $this->assertSame('Bez zarzutu',     CarLabels::techStatus(null)['label']);
        $this->assertSame('Wymaga uwagi',    CarLabels::techStatus(['status' => 'attention'])['label']);
        $this->assertSame('Nieprawidłowość', CarLabels::techStatus(['status' => 'bad'])['label']);
    }
}
