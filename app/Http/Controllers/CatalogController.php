<?php

namespace App\Http\Controllers;

use App\Helpers\CarLabels;
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
        // Fuel-type filter normalised: dropdown emits a canonical Polish label
        // ("Benzyna"/"Diesel"/"Hybryda"), DB rows may have raw input
        // ("benzyna", "petrol", "elektryczny"). Pull every distinct raw value
        // that normalises (via CarLabels::fuelType) to the selected canon and
        // match against the union — covers case + synonym variance in one
        // pass with a single whereIn.
        if (!empty($filters['fuel_type'])) {
            $canon = CarLabels::fuelType((string) $filters['fuel_type']) ?? $filters['fuel_type'];
            $rawMatches = Car::query()
                ->whereNotNull('fuel_type')
                ->distinct()
                ->pluck('fuel_type')
                ->filter(fn($v) => (CarLabels::fuelType((string) $v) ?? $v) === $canon)
                ->values()
                ->all();
            if ($rawMatches) {
                $query->whereIn('fuel_type', $rawMatches);
            } else {
                // Selected fuel exists in dropdown but no rows currently store
                // that canon — force empty result instead of returning all.
                $query->whereRaw('1 = 0');
            }
        }
        if (isset($filters['price_min']))     $query->where('price', '>=', $filters['price_min']);
        if (isset($filters['price_max']))     $query->where('price', '<=', $filters['price_max']);
        // Year-range filtering needs an integer column. `first_registration` is
        // stored as "MM/YYYY" — a lexicographic string compare misorders
        // "10/2015" < "2/2024" and breaks the range. Prefer `production_year`
        // (integer, filled by wizard Step 1); for legacy rows missing it, fall
        // back to the 4-digit year parsed out of `first_registration` via SQL.
        // SUBSTR works in both MySQL and SQLite; CAST AS INTEGER for SQLite,
        // UNSIGNED for MySQL.
        $intCast = \DB::connection()->getDriverName() === 'sqlite' ? 'INTEGER' : 'UNSIGNED';
        $yearExpr = "COALESCE(production_year, CAST(SUBSTR(first_registration, -4) AS {$intCast}))";
        if (!empty($filters['year_min'])) {
            $query->whereRaw("{$yearExpr} >= ?", [(int) $filters['year_min']]);
        }
        if (!empty($filters['year_max'])) {
            $query->whereRaw("{$yearExpr} <= ?", [(int) $filters['year_max']]);
        }
        if (isset($filters['mileage_min']))   $query->where('mileage', '>=', $filters['mileage_min']);
        if (isset($filters['mileage_max']))   $query->where('mileage', '<=', $filters['mileage_max']);
        if (isset($filters['power_min']))     $query->where('power_hp', '>=', $filters['power_min']);
        if (isset($filters['power_max']))     $query->where('power_hp', '<=', $filters['power_max']);
        if (!empty($filters['category'])) {
            // Body-type filter. Match either column (admin fills `body_type`
            // for new cars; legacy rows may only have `category` set) and
            // ignore casing so "?category=SUV" hits rows with body_type="suv".
            // CarLabels::bodyType normalises both sides to one canonical
            // Polish display label, then we lower-compare.
            $canon = mb_strtolower((string) (CarLabels::bodyType($filters['category']) ?? $filters['category']));
            $query->where(function ($q) use ($canon) {
                $q->whereRaw('LOWER(TRIM(body_type)) = ?', [$canon])
                  ->orWhereRaw('LOWER(TRIM(category)) = ?', [$canon]);
            });
        }
        if (!empty($filters['transmission'])) $query->where('transmission', 'like', '%' . $filters['transmission'] . '%');

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir   = $filters['dir']  ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        $cars = $query->paginate(12)->withQueryString();

        [$brands, $categories, $fuelTypes] = Cache::remember('catalog.filters', 600, function () {
            // Body-type dropdown sources from BOTH body_type (admin's current
            // field) AND legacy category — normalised through CarLabels::bodyType
            // so admin's "suv" / "SUV" / "Suv" / "wagon" collapse to one tile
            // label and SUV-equipped cars stop being invisible because the
            // dropdown previously sourced only from `category`.
            $rawBodyTypes = Car::available()
                ->whereRaw('(body_type IS NOT NULL OR category IS NOT NULL)')
                ->selectRaw("COALESCE(NULLIF(TRIM(body_type), ''), NULLIF(TRIM(category), '')) as raw_bt")
                ->distinct()
                ->pluck('raw_bt');
            $categories = $rawBodyTypes
                ->map(fn($v) => CarLabels::bodyType((string) $v))
                ->filter()
                ->unique()
                ->sort()
                ->values();
            // Fuel-type dropdown: same normalisation pass so "benzyna" and
            // "Benzyna" collapse into one option labelled "Benzyna".
            $fuelTypes = Car::available()
                ->whereNotNull('fuel_type')
                ->distinct()
                ->pluck('fuel_type')
                ->map(fn($v) => CarLabels::fuelType((string) $v) ?? $v)
                ->filter()
                ->unique()
                ->sort()
                ->values();
            return [
                Brand::orderBy('name')->get(),
                $categories,
                $fuelTypes,
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

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires', 'pano360Image', 'exteriorPano360Image');

        $prevCar = Car::available()
            ->where('created_at', '>', $car->created_at)
            ->orderBy('created_at', 'asc')
            ->first(['id', 'slug']);

        $nextCar = Car::available()
            ->where('created_at', '<', $car->created_at)
            ->orderBy('created_at', 'desc')
            ->first(['id', 'slug']);

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

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires', 'pano360Image', 'exteriorPano360Image');

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

        $carId     = $car->id;
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $ip        = $request->ip();
        $referer   = substr((string) $request->headers->get('referer'), 0, 500) ?: null;

        defer(function () use ($carId, $sessionId, $ip, $referer, $ua) {
            try {
                $already = \App\Models\CarView::where('car_id', $carId)
                    ->where('session_id', $sessionId)
                    ->where('created_at', '>=', now()->subMinutes(30))
                    ->exists();

                if ($already) return;

                \App\Models\CarView::create([
                    'car_id'     => $carId,
                    'session_id' => $sessionId,
                    'ip'         => $ip,
                    'referer'    => $referer,
                    'user_agent' => substr($ua, 0, 500) ?: null,
                ]);
            } catch (\Throwable) {
                // Silent.
            }
        });
    }
}
