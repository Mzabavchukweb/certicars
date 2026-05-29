<?php

namespace App\Http\Controllers;

use App\Helpers\CarLabels;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        [$featuredCars, $totalCars, $brands, $bodyTypeCounts] = Cache::remember('home.content', 300, function () {
            $featuredCars = Car::with(['brand', 'images'])
                ->available()
                ->featured()
                ->latest()
                ->take(6)
                ->get();

            $totalCars = Car::available()->count();

            $brands = Brand::withCount(['cars' => fn($q) => $q->available()])
                ->get()
                ->filter(fn($b) => $b->cars_count > 0);

            // Body-type counts on the home page must reflect the field admins
            // actually fill (`body_type`). The legacy `category` column survives
            // for a handful of older rows so we COALESCE the two before
            // grouping, then canonicalize each raw value through
            // CarLabels::bodyType() — that converts "suv" / "SUV" / "Suv" /
            // "wagon" / "minivan" into the six tile labels rendered on the
            // home page, so admin casing inconsistencies stop producing 0s.
            $rows = Car::available()
                ->whereRaw('(body_type IS NOT NULL OR category IS NOT NULL)')
                ->selectRaw("COALESCE(NULLIF(TRIM(body_type), ''), NULLIF(TRIM(category), '')) as raw_bt, COUNT(*) as total")
                ->groupBy('raw_bt')
                ->pluck('total', 'raw_bt');

            $bodyTypeCounts = [];
            foreach ($rows as $raw => $total) {
                $label = CarLabels::bodyType((string) $raw);
                if (!$label) continue;
                $bodyTypeCounts[$label] = ($bodyTypeCounts[$label] ?? 0) + (int) $total;
            }
            // Return as a plain array keyed by display label so the Blade can
            // do `$bodyTypeCounts['SUV']` safely.

            return [$featuredCars, $totalCars, $brands, $bodyTypeCounts];
        });

        return view('home', compact('featuredCars', 'totalCars', 'brands', 'bodyTypeCounts'));
    }
}
