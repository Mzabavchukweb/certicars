<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $audi = Brand::where('name', 'Audi')->first();

        $car = Car::create([
            'brand_id' => $audi->id,
            'model' => 'A4 2.0 TFSI Quattro',
            'category' => 'Sedan',
            'body_type' => 'Sedan',
            'price' => 74500,
            'currency' => 'PLN',
            'price_type' => 'Brutto (VAT marża)',
            'color' => 'Florett Silver',
            'color_code' => 'FLORETTSILBER',
            'doors' => '4/5',
            'seats' => 5,
            'weight' => 1600,
            'upholstery' => 'Skóra nappa czarna',
            'vin' => 'WAUZZZ8K9JA567890',
            'first_registration' => '11/2018',
            'mileage' => 105200,
            'previous_owners' => 1,
            'number_of_keys' => 2,
            'business_use' => 'Użytkowanie prywatne',
            'fuel_type' => 'Benzyna',
            'power_hp' => 252,
            'power_kw' => 185,
            'engine_capacity' => 1984,
            'transmission' => 'Automatyczna skrzynia biegów',
            'transmission_detail' => 'S tronic 7-bieg.',
            'location' => 'Warszawa',
            'is_imported' => true,
            'country_registration' => 'Niemcy',
            'taxation' => 'VAT marża',
            'last_service' => '03/2025',
            'last_service_mileage' => 98500,
            'next_inspection' => '09/2026',
            'service_documentation' => 'Kompletna dokumentacja serwisowa',
            'service_book' => 'Dostępna — cyfrowa i papierowa',
            'coc_documents' => 'Tak — oryginalne',
            'vehicle_folder' => 'Kompletna teczka pojazdu dostępna',
            'hu_au_report' => 'Ważny do 09/2026 — bez zastrzeżeń',
            'fuel_consumption' => '7.2',
            'fuel_procedure' => 'WLTP',
            'co2_emission' => '163',
            'emission_class' => 'Euro 6d',
            'seller_name' => 'CertiCars Sp. z o.o.',
            'seller_phone' => '+48 22 123 45 67',
            'seller_email' => 'sprzedaz@certicars.pl',
            'commission_note' => 'Pojazd sprawdzony i zweryfikowany przez CertiCars.',
            'reception_date' => '2025-04-10',
            'is_featured' => true,
            'status' => 'active',
            'meta_title' => 'Audi A4 2.0 TFSI Quattro 2018 — 252 KM, S tronic | CertiCars',
            'meta_description' => 'Sprawdzone Audi A4 2.0 TFSI Quattro z 2018 roku. 252 KM, napęd quattro, S tronic. Pełna historia serwisowa, raport CertiCheck. Sprawdź szczegóły i umów się na jazdę próbną.',
            'focus_keyword' => 'Audi A4 TFSI Quattro',
            'paint_measurements' => [
                ['label' => 'Maska', 'value' => '118 μm'],
                ['label' => 'Błotnik prz. lewy', 'value' => '125 μm'],
                ['label' => 'Błotnik prz. prawy', 'value' => '120 μm'],
                ['label' => 'Drzwi przód lewe', 'value' => '122 μm'],
                ['label' => 'Drzwi przód prawe', 'value' => '119 μm'],
                ['label' => 'Drzwi tył lewe', 'value' => '121 μm'],
                ['label' => 'Drzwi tył prawe', 'value' => '123 μm'],
                ['label' => 'Dach', 'value' => '117 μm'],
                ['label' => 'Klapa bagażnika', 'value' => '124 μm'],
                ['label' => 'Błotnik tył lewy', 'value' => '128 μm'],
                ['label' => 'Błotnik tył prawy', 'value' => '126 μm'],
            ],
            'technical_conditions' => [
                'engine' => 'Olej wymieniony. Brak wycieków.',
                'transmission' => 'Brak slippage. Praca płynna.',
                'suspension' => 'Nowe amortyzatory przód i tył.',
                'electronics' => 'Wszystko sprawne. Brak błędów DTC.',
                'body' => 'Lakier oryginalny fabryczny na wszystkich elementach.',
                'brakes' => 'Klocki: przód 70%, tył 60%. Tarcze bez bicia.',
                'steering' => 'Układ kierowniczy precyzyjny. Brak luzów.',
                'exhaust' => 'Układ wydechowy szczelny, bez korozji.',
                'ac' => 'Klimatyzacja sprawna, ostatnie napełnienie 04/2025.',
            ],
            'equipment' => [
                'Komfort' => [
                    'Klimatyzacja automatyczna (3-strefowa)',
                    'Reflektory LED High Performance',
                    'System nawigacyjny MMI z kartą SD',
                    'Szyberdach panoramiczny',
                    'Kamera cofania',
                    'Czujniki parkowania przód i tył',
                    'Apple CarPlay / Android Auto',
                    'Lusterka zewnętrzne elektrycznie składane i ogrzewane',
                    'Podgrzewane przednie fotele',
                    'System audio B&O Sound System',
                    'Audi Virtual Cockpit',
                    'Elektrycznie regulowane fotele z pamięcią',
                    'Kierownica wielofunkcyjna ze sterowaniem głosowym',
                    'Bezprzewodowe ładowanie telefonu',
                ],
                'Bezpieczeństwo' => [
                    'ABS / ESP',
                    'Asystent utrzymania pasa ruchu',
                    'Asystent zmiany pasa ruchu',
                    'Tempomat adaptacyjny (ACC)',
                    'System ostrzegania o kolizji',
                    'Kontrola odstępów w zakrętach',
                    'System start/stop',
                    'Centralny zamek z pilotem',
                    'Światła do jazdy dziennej LED',
                    'Lampy tylne LED',
                    'Automatyczne włączanie świateł',
                    'System monitorowania martwego pola',
                    'Kamera 360°',
                ],
                'Dodatkowe' => [
                    'Felgi czarne aluminiowe 18"',
                    'Quattro — stały napęd na 4 koła',
                    'Dywaniki welurowe',
                    'Pakiet zimowy',
                    'Czujnik zmierzchu i deszczu',
                    'System Keyless Entry / Go',
                    'Zamek centralny z pilotem',
                    'Hak holowniczy elektrycznie składany',
                    'Przyciemniane szyby tylne',
                    'Relingi dachowe aluminiowe',
                ],
            ],
        ]);

        // Audi A4 — gallery images
        $audiImages = [
            ['https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=1200&q=80', true],
            ['https://images.unsplash.com/photo-1541443131876-a4246e0b1fc6?w=1200&q=80', false],
            ['https://images.unsplash.com/photo-1614200179396-2bdb77ebf81b?w=1200&q=80', false],
        ];
        foreach ($audiImages as $idx => [$url, $isPrimary]) {
            $car->images()->create([
                'path' => $url,
                'type' => 'exterior',
                'is_primary' => $isPrimary,
                'sort_order' => $idx,
            ]);
        }

        $damages = [
            ['Maska silnika', 'damage', ['Drobne zarysowania'], 'Drobne zarysowania na krawędzi maski. Nie wpływa na integralność.', 50, 18],
            ['Błotnik przedni lewy', 'damage', ['Otarcie'], 'Otarcie lakieru 3cm.', 22, 30],
            ['Drzwi kierowcy', 'damage', ['Mikro-rysy'], 'Mikro-rysy od codziennego użytkowania.', 24, 50],
            ['Drzwi pasażera', 'damage', ['Wgniecenie'], 'Małe wgniecenie 2cm.', 78, 50],
            ['Błotnik tylny lewy', 'damage', ['Rysa'], 'Rysa przy łuku koła.', 22, 72],
            ['Drzwi tylne lewe', 'damage', ['Rysa'], 'Rysa wzdłuż dolnej krawędzi.', 24, 62],
            ['Zderzak przedni', 'damage', ['Otarcie'], 'Otarcie 5cm.', 50, 8],
            ['Próg lewy', 'damage', ['Rysa'], null, 12, 58],
            ['Próg prawy', 'repaired', ['Naprawiony'], 'Naprawiony profesjonalnie.', 88, 58],
            ['Zderzak tylny', 'damage', ['Zadrapanie'], null, 50, 90],
            ['Drzwi tylne prawe', 'damage', ['Mikro-rysy'], null, 76, 62],
            ['Błotnik tylny prawy', 'damage', ['Wgniecenie'], 'Małe wgniecenie po parkingu.', 78, 72],
        ];
        foreach ($damages as [$area, $type, $tags, $desc, $x, $y]) {
            $car->damages()->create([
                'area' => $area,
                'type' => $type,
                'severity' => $type === 'damage' ? 'warning' : 'info',
                'tags' => $tags,
                'description' => $desc,
                'position_x' => $x,
                'position_y' => $y,
            ]);
        }

        $set1 = $car->tireSets()->create([
            'set_number' => 1,
            'is_mounted' => true,
            'tire_type' => 'Opony letnie 225/50 R18',
            'rim' => '18" Aluminium — 5-ramienne oryginalne Audi',
            'notes' => 'Komplet w dobrym stanie, rok produkcji 2023',
        ]);
        foreach ([
            ['Przednia lewa', '6 mm', []],
            ['Przednia prawa', '6 mm', []],
            ['Tylna lewa', '5 mm', []],
            ['Tylna prawa', '5 mm', ['Nierównomierne zużycie']],
        ] as [$pos, $depth, $cond]) {
            $set1->tires()->create([
                'position' => $pos,
                'tread_depth' => $depth,
                'condition' => $cond,
            ]);
        }

        $set2 = $car->tireSets()->create([
            'set_number' => 2,
            'is_mounted' => false,
            'tire_type' => 'Opony zimowe 225/50 R18',
            'rim' => '18" Stalowe z kołpakami Audi',
            'notes' => 'Komplet zimowy, rok produkcji 2022 — przechowywane w magazynie',
        ]);
        foreach ([
            ['Przednia lewa', '5 mm', []],
            ['Przednia prawa', '4.5 mm', []],
            ['Tylna lewa', '4 mm', ['Lekkie zużycie boczne']],
            ['Tylna prawa', '4.5 mm', []],
        ] as [$pos, $depth, $cond]) {
            $set2->tires()->create([
                'position' => $pos,
                'tread_depth' => $depth,
                'condition' => $cond,
            ]);
        }

        // Dodatkowe samochody z prawdziwymi zdjęciami
        $samples = [
            [
                'brand' => 'BMW',
                'model' => '320d M Sport',
                'price' => 89900,
                'color' => 'Czarny metalik',
                'reg' => '06/2019',
                'mileage' => 87450,
                'hp' => 190,
                'fuel' => 'Diesel',
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1200&q=80',
            ],
            [
                'brand' => 'Audi',
                'model' => 'A6 3.0 TDI Quattro',
                'price' => 112000,
                'color' => 'Srebrny metalik',
                'reg' => '09/2020',
                'mileage' => 62000,
                'hp' => 272,
                'fuel' => 'Diesel',
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80',
            ],
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf VII 1.4 TSI Highline',
                'price' => 52900,
                'color' => 'Biały',
                'reg' => '03/2018',
                'mileage' => 68700,
                'hp' => 150,
                'fuel' => 'Benzyna',
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1471444928139-48c5bf5173f8?w=1200&q=80',
            ],
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'C 220d AMG Line',
                'price' => 124500,
                'color' => 'Czarny obsydian',
                'reg' => '06/2021',
                'mileage' => 62000,
                'hp' => 194,
                'fuel' => 'Diesel',
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=1200&q=80',
            ],
            [
                'brand' => 'Porsche',
                'model' => '911 Carrera S',
                'price' => 425000,
                'color' => 'Guards Red',
                'reg' => '01/2019',
                'mileage' => 48000,
                'hp' => 450,
                'fuel' => 'Benzyna',
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1580274455191-1c62238fa1c4?w=1200&q=80',
            ],
            [
                'brand' => 'BMW',
                'model' => 'X5 xDrive30d M Sport',
                'price' => 189000,
                'color' => 'Szary manhattan',
                'reg' => '09/2021',
                'mileage' => 41000,
                'hp' => 265,
                'fuel' => 'Diesel',
                'featured' => false,
                'image' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=1200&q=80',
            ],
        ];

        foreach ($samples as $s) {
            $brand = Brand::where('name', $s['brand'])->first();
            if (!$brand) continue;
            $newCar = Car::create([
                'brand_id' => $brand->id,
                'model' => $s['model'],
                'category' => 'Sedan',
                'body_type' => 'Sedan',
                'price' => $s['price'],
                'currency' => 'PLN',
                'color' => $s['color'],
                'first_registration' => $s['reg'],
                'mileage' => $s['mileage'],
                'power_hp' => $s['hp'],
                'fuel_type' => $s['fuel'],
                'transmission' => 'Automatyczna skrzynia biegów',
                'is_imported' => true,
                'country_registration' => 'Niemcy',
                'is_featured' => $s['featured'],
                'status' => 'active',
            ]);
            $newCar->images()->create([
                'path' => $s['image'],
                'type' => 'exterior',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
