<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'brand'        => 'nullable|integer|exists:brands,id',
            'fuel_type'    => 'nullable|string|max:50',
            'price_min'    => 'nullable|numeric|min:0',
            'price_max'    => 'nullable|numeric|min:0',
            'year_min'     => 'nullable|string|max:10',
            'year_max'     => 'nullable|string|max:10',
            'mileage_min'  => 'nullable|numeric|min:0',
            'mileage_max'  => 'nullable|numeric|min:0',
            'power_min'    => 'nullable|numeric|min:0',
            'power_max'    => 'nullable|numeric|min:0',
            'category'     => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'sort'         => 'nullable|in:price,mileage,created_at,first_registration',
            'dir'          => 'nullable|in:asc,desc',
        ]);

        $query = Car::with(['brand', 'images'])->available();

        if (!empty($filters['brand']))        $query->where('brand_id', $filters['brand']);
        if (!empty($filters['fuel_type']))    $query->where('fuel_type', $filters['fuel_type']);
        if (isset($filters['price_min']))     $query->where('price', '>=', $filters['price_min']);
        if (isset($filters['price_max']))     $query->where('price', '<=', $filters['price_max']);
        if (!empty($filters['year_min']))     $query->where('first_registration', '>=', $filters['year_min']);
        if (!empty($filters['year_max']))     $query->where('first_registration', '<=', $filters['year_max']);
        if (isset($filters['mileage_min']))   $query->where('mileage', '>=', $filters['mileage_min']);
        if (isset($filters['mileage_max']))   $query->where('mileage', '<=', $filters['mileage_max']);
        if (isset($filters['power_min']))     $query->where('power_hp', '>=', $filters['power_min']);
        if (isset($filters['power_max']))     $query->where('power_hp', '<=', $filters['power_max']);
        if (!empty($filters['category']))     $query->where('category', $filters['category']);
        if (!empty($filters['transmission'])) $query->where('transmission', 'like', '%' . $filters['transmission'] . '%');

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir   = $filters['dir']  ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        $cars = $query->paginate(12)->withQueryString();

        [$brands, $categories, $fuelTypes] = Cache::remember('catalog.filters', 600, function () {
            return [
                Brand::orderBy('name')->get(),
                Car::available()->whereNotNull('category')->distinct()->pluck('category'),
                Car::available()->whereNotNull('fuel_type')->distinct()->pluck('fuel_type'),
            ];
        });

        return view('catalog.index', compact('cars', 'brands', 'categories', 'fuelTypes'));
    }

    public function show(Request $request, Car $car)
    {
        if ($car->status !== 'active' && !(auth()->user()?->is_admin)) {
            abort(404);
        }

        $this->trackCarView($request, $car);

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires', 'pano360Image', 'exteriorPano360Image');

        // Prev / Next navigation (ordered by created_at desc, same as catalog index)
        $prevCar = Car::available()
            ->where('created_at', '>', $car->created_at)
            ->orderBy('created_at', 'asc')
            ->first(['id', 'slug']);

        $nextCar = Car::available()
            ->where('created_at', '<', $car->created_at)
            ->orderBy('created_at', 'desc')
            ->first(['id', 'slug']);

        // Related cars: same brand first, then fill with other cars to always get 3
        $relatedCars = Car::with(['brand', 'images'])
            ->available()
            ->where('id', '!=', $car->id)
            ->where('brand_id', $car->brand_id)
            ->take(3)
            ->get();

        if ($relatedCars->count() < 3) {
            $excludeIds = $relatedCars->pluck('id')->push($car->id)->toArray();
            $filler = Car::with(['brand', 'images'])
                ->available()
                ->whereNotIn('id', $excludeIds)
                ->inRandomOrder()
                ->take(3 - $relatedCars->count())
                ->get();
            $relatedCars = $relatedCars->concat($filler);
        }

        return view('catalog.show', compact('car', 'relatedCars', 'prevCar', 'nextCar'));
    }

    public function certicheck(Car $car)
    {
        $isAdmin = auth()->user()?->is_admin;

        if ($car->status !== 'active' && !$isAdmin) {
            abort(404);
        }

        if (! $car->has_certicheck && !$isAdmin) {
            abort(404);
        }

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires', 'pano360Image', 'exteriorPano360Image');

        return view('catalog.certicheck', compact('car'));
    }

    private function trackCarView(Request $request, Car $car): void
    {
        if (auth()->user()?->is_admin) return;

        $ua = strtolower((string) $request->userAgent());
        if ($ua === '') return;
        foreach (['bot', 'crawl', 'spider', 'slurp', 'bing', 'duckduck', 'lighthouse', 'headless', 'curl', 'wget'] as $p) {
            if (str_contains($ua, $p)) return;
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;

        $already = \App\Models\CarView::where('car_id', $car->id)
            ->where('session_id', $sessionId)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($already) return;

        try {
            \App\Models\CarView::create([
                'car_id'     => $car->id,
                'session_id' => $sessionId,
                'ip'         => $request->ip(),
                'referer'    => substr((string) $request->headers->get('referer'), 0, 500) ?: null,
                'user_agent' => substr($ua, 0, 500) ?: null,
            ]);
        } catch (\Throwable $e) {
            // Silent.
        }
    }
}
