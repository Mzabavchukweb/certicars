<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;

class HomeController extends Controller
{
    public function index()
    {
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

        $bodyTypeCounts = Car::available()
            ->selectRaw('category, COUNT(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('home', compact('featuredCars', 'totalCars', 'brands', 'bodyTypeCounts'));
    }
}
