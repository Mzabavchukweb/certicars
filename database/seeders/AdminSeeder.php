<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
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
