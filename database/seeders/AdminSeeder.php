<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@certicars.pl')],
            [
                'name' => env('ADMIN_NAME', 'Admin CertiCars'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Grubaba123')),
                'is_admin' => true,
            ]
        );
    }
}
