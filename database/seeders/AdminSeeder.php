<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    // ADMIN_PASSWORD is SEED-ONLY. It is read exactly once — when the admin
    // user row does not yet exist in the database. After that, Auth::attempt()
    // checks the bcrypt hash stored in users.password; ADMIN_PASSWORD is never
    // read again by the application at runtime.
    //
    // Consequence: changing ADMIN_PASSWORD in Railway env does NOT change the
    // login password of an existing admin. The env var becomes a stale label.
    //
    // Safe ways to change the production admin password:
    //   Option A (preferred): Log in to /admin, go to Profile → Change password.
    //   Option B (env-driven reset): Set ADMIN_FORCE_PASSWORD_RESET=true and
    //     ADMIN_PASSWORD=<new_password> in Railway, then run:
    //       php artisan db:seed --class=AdminSeeder --force
    //     Remove ADMIN_FORCE_PASSWORD_RESET afterwards to prevent re-reset on
    //     every seeder run.

    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            // Production must provide credentials via environment.
            // Locally, fall back to dev defaults but log a warning.
            if (app()->environment('production')) {
                $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set — skipping admin seeding.');
                return;
            }
            $email    = $email    ?? 'admin@certicars.local';
            $password = $password ?? Str::random(20);
            $this->command?->warn("Dev admin: {$email} / {$password}");
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = env('ADMIN_NAME', $user->name ?? 'Admin CertiCars');
        $user->is_admin = true;

        // Only set the password if the user is new, or admin explicitly
        // requested a reset via ADMIN_FORCE_PASSWORD_RESET=true.
        if (! $user->exists || env('ADMIN_FORCE_PASSWORD_RESET', false)) {
            $user->password = Hash::make($password);
        }

        $user->save();
    }
}
