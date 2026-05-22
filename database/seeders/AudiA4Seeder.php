<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Car;

class AudiA4Seeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/audi_a4_seed.json'), true);

        // Brand
        $brand = Brand::firstOrCreate(
            ['slug' => $data['brand']['slug']],
            ['name' => $data['brand']['name']]
        );

        // Car
        $carData = $data['car'];
        $carData['brand_id'] = $brand->id;

        // Remove timestamps to let Eloquent handle them
        unset($carData['created_at'], $carData['updated_at']);

        // firstOrCreate — never overwrite existing dealer data on subsequent deploys.
        // updateOrCreate here would reset all admin edits (price, status, SEO…) on every restart.
        $car = Car::firstOrCreate(
            ['slug' => $carData['slug']],
            $carData
        );

        // Images
        if (!$car->images()->exists()) {
            foreach ($data['images'] as $img) {
                unset($img['created_at'], $img['updated_at']);
                $car->images()->create($img);
            }
        }

        // Damages
        if (!$car->damages()->exists()) {
            foreach ($data['damages'] as $dmg) {
                unset($dmg['created_at'], $dmg['updated_at']);
                $car->damages()->create($dmg);
            }
        }

        $this->command->info("✅ Audi A4 2.0 TFSI Quattro seeded (ID: {$car->id})");
    }
}
