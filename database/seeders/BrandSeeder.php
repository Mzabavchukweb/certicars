<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Audi', 'BMW', 'Mercedes-Benz', 'Volkswagen', 'Porsche', 'Volvo', 'Toyota', 'Lexus'];
        foreach ($brands as $name) {
            Brand::firstOrCreate(['name' => $name]);
        }
    }
}
