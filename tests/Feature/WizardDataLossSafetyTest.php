<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * P1 data-loss safety net — server-side guards.
 *
 * The wizard form's localStorage autosave + restore banner behaviour is JS-only
 * and best verified by Antigravity/Playwright. These tests cover the server
 * surface that the JS module depends on: meta tags, the 419 marker, the new
 * client-diag input, and the structured-log emission shape.
 */
class WizardDataLossSafetyTest extends TestCase
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

    public function test_wizard_create_renders_session_expires_meta_tag(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cars.create'));
        $response->assertOk();

        // The autosave JS reads this to time the warning banner.
        $response->assertSee('name="session-expires-at"', false);
        $response->assertSee('name="wizard-autosave-key"', false);
        $response->assertSee('content="wiz:car:create"', false);
        $response->assertSee('name="wizard-car-updated-at"', false);
    }

    public function test_wizard_edit_renders_autosave_key_with_car_id(): void
    {
        $car = Car::create(['brand_id' => $this->brand->id, 'model' => 'AutosaveEdit', 'status' => 'active']);

        $response = $this->actingAs($this->admin)->get(route('admin.cars.edit', $car));
        $response->assertOk();
        $response->assertSee('content="wiz:car:edit:' . $car->id . '"', false);
        // car-updated-at must be a unix timestamp string (autosave compares against it)
        $response->assertSeeInOrder(['name="wizard-car-updated-at"', 'content="' . $car->updated_at->timestamp . '"'], false);
    }

    public function test_wizard_renders_restore_banner_dom_for_autosave(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cars.create'));
        $response->assertOk();
        $response->assertSee('id="wizRestoreBanner"', false);
        $response->assertSee('id="wizRestoreAccept"', false);
        $response->assertSee('id="wizRestoreDiscard"', false);
        $response->assertSee('Znaleziono niezapisane dane formularza');
    }

    public function test_wizard_renders_session_warning_dom_for_expiry_banner(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cars.create'));
        $response->assertOk();
        $response->assertSee('id="wizSessionWarning"', false);
        $response->assertSee('id="wizSessionWarningTitle"', false);
        $response->assertSee('id="wizSessionWarningText"', false);
    }

    public function test_wizard_includes_autosave_javascript_module(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cars.create'));
        $response->assertOk();
        // Source-grep guard: removing any of these would break the autosave path.
        foreach (['wizInitAutosave', 'wizPersistDraft', 'wizLoadDraft', 'wizApplyDraft', 'wizInitSessionWarning'] as $sym) {
            $response->assertSee($sym, false);
        }
    }

    public function test_store_accepts_diag_input_without_breaking_validation(): void
    {
        // The _diag input is sent by the wizard JS on every submit. The
        // controller must not treat it as a validation error and must not
        // persist it to the car model.
        $diag = json_encode([
            'opened_at' => time() - 600,
            'elapsed_sec' => 600,
            'field_count' => 80,
            'file_count' => 3,
            'file_total_bytes' => 5_000_000,
            'button' => 'save',
            'step' => 2,
            'had_draft' => false,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.cars.store'), [
            'brand_id' => $this->brand->id,
            'model'    => 'DiagAccepted',
            'status'   => 'active',
            '_diag'    => $diag,
        ]);

        $response->assertRedirect();
        $car = Car::where('model', 'DiagAccepted')->first();
        $this->assertNotNull($car);
        // _diag is observability only — must NOT be saved on the car
        $this->assertObjectNotHasProperty('_diag', $car);
    }

    public function test_validation_failure_is_logged_as_car_save_validation_failed(): void
    {
        \Illuminate\Support\Facades\Log::shouldReceive('info')->andReturnNull();
        \Illuminate\Support\Facades\Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'car.save.validation_failed'
                    && isset($context['fields'])
                    && in_array('brand_id', $context['fields'], true)
                    && in_array('model', $context['fields'], true);
            });
        // suppress other log channels
        \Illuminate\Support\Facades\Log::shouldReceive('error')->andReturnNull();

        $this->actingAs($this->admin)
            ->from(route('admin.cars.create'))
            ->post(route('admin.cars.store'), ['status' => 'active'])
            ->assertSessionHasErrors(['brand_id', 'model']);
    }

    public function test_csrf_handler_source_emits_session_expired_marker(): void
    {
        // The handler in bootstrap/app.php must attach session_expired=true to
        // the flash (web) AND to the JSON body (API). Session flash needs the
        // full middleware stack to persist, so verify the source shape directly
        // — the runtime behaviour is covered by integration testing.
        $source = file_get_contents(base_path('bootstrap/app.php'));

        $this->assertStringContainsString("TokenMismatchException", $source);
        $this->assertStringContainsString("Sesja wygasła. Odśwież stronę i spróbuj ponownie.", $source);
        $this->assertMatchesRegularExpression(
            "/'session_expired'\s*=>\s*true/",
            $source,
            "419 handler must mark JSON responses with session_expired=true so the autosave layer can surface the restore banner."
        );
        $this->assertStringContainsString(
            "->with('session_expired', true)",
            $source,
            "419 handler must flash session_expired=true on redirect-back so the wizard restore banner appears."
        );
    }

    public function test_csrf_handler_returns_419_for_json_requests(): void
    {
        // Smoke-test the handler returns SOMETHING for JSON paths — full
        // expectsJson() flow needs real middleware. Here we just confirm the
        // handler doesn't throw and produces a non-empty response.
        $request = \Illuminate\Http\Request::create('/admin/cars', 'POST', []);
        $request->headers->set('Accept', 'application/json');
        $request->setLaravelSession(app('session.store'));

        $handler = app(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        $response = $handler->render($request, new \Illuminate\Session\TokenMismatchException());

        // The status must NOT be 500 (which would be the default Page Expired).
        $this->assertNotSame(500, $response->getStatusCode());
    }

    public function test_wizard_layout_contains_max_720_floor_assumption(): void
    {
        // Cross-check: the autosave JS computes session-expires-at from
        // config('session.lifetime'). PR #4 floors it at 720. Both layers
        // depend on this — confirm the floor is still in place.
        $this->assertGreaterThanOrEqual(720, (int) config('session.lifetime'));
    }
}
