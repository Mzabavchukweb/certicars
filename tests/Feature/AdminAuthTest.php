<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name'     => 'Admin',
            'email'    => 'admin@test.pl',
            'password' => Hash::make('secret123'),
            'is_admin' => true,
        ]);
    }

    private function user(): User
    {
        return User::create([
            'name'     => 'User',
            'email'    => 'user@test.pl',
            'password' => Hash::make('secret123'),
            'is_admin' => false,
        ]);
    }

    public function test_admin_login_ok(): void
    {
        $this->admin();
        $this->post('/admin/login', ['email' => 'admin@test.pl', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        $this->admin();
        $this->post('/admin/login', ['email' => 'admin@test.pl', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_routes_require_auth(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/cars')->assertRedirect(route('login'));
    }

    public function test_non_admin_user_gets_403(): void
    {
        $user = $this->user();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }

    public function test_logout_works(): void
    {
        $this->actingAs($this->admin())->post('/admin/logout')->assertRedirect();
        $this->assertGuest();
    }

    public function test_password_reset_request_form_loads(): void
    {
        $this->get(route('password.request'))->assertOk()->assertSee('Reset hasła');
    }

    public function test_password_reset_sends_link(): void
    {
        $this->admin();
        $this->post(route('password.email'), ['email' => 'admin@test.pl'])
            ->assertSessionHas('status');
    }

    public function test_password_reset_form_loads(): void
    {
        $this->get(route('password.reset', ['token' => 'sometoken']))
            ->assertOk()
            ->assertSee('Nowe hasło');
    }
}
