<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with(['brand', 'images'])->withCount('views');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $allowedSort = ['created_at', 'price', 'mileage', 'first_registration', 'model', 'views'];
        $sort = in_array($request->get('sort'), $allowedSort, true) ? $request->sort : 'created_at';
        $dir  = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        if ($sort === 'views') {
            $query->orderBy('views_count', $dir);
        } else {
            $query->orderBy($sort, $dir);
        }

        if ($request->get('format') === 'json') {
            $cars = $query->limit(10)->get();
            return response()->json([
                'cars' => $cars->map(fn($c) => [
                    'id'         => $c->id,
                    'title'      => $c->title,
                    'identifier' => $c->identifier,
                    'price'      => $c->formatted_price,
                    'image'      => $c->primaryImage?->url,
                    'edit_url'   => route('admin.cars.edit', $c),
                ]),
            ]);
        }

        $cars   = $query->paginate(15)->withQueryString();
        $brands = Brand::orderBy('name')->get();

        return view('admin.cars.index', compact('cars', 'brands', 'sort', 'dir'));
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:featured,unfeatured,sold,active,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:cars,id',
        ]);

        $cars = Car::whereIn('id', $request->ids);

        match ($request->action) {
            'featured'   => $cars->update(['is_featured' => true]),
            'unfeatured' => $cars->update(['is_featured' => false]),
            'sold'       => $cars->update(['is_sold' => true, 'status' => 'sold']),
            'active'     => $cars->update(['is_sold' => false, 'status' => 'active']),
            'delete'     => $this->bulkDelete($cars->get()),
        };

        Cache::forget('catalog.filters');
        return back()->with('success', 'Akcja wykonana na ' . count($request->ids) . ' samochodach.');
    }

    private function bulkDelete($cars): void
    {
        foreach ($cars as $car) {
            foreach ($car->images as $image) {
                if (!str_starts_with($image->path, 'http')) {
                    Storage::disk('public')->delete($image->path);
                }
            }
            $car->delete();
        }
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('admin.cars.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCar($request);
        $validated = $this->processEquipment($validated);
        unset($validated['engine_video_file'], $validated['remove_engine_video'], $validated['image_alt']);
        $car = Car::create($validated);

        $this->handleEngineVideo($car, $request);
        $this->syncRelations($car, $request);
        $this->handleImages($car, $request);

        Cache::forget('catalog.filters');

        return redirect()->route('admin.cars.edit', $car)
            ->with('success', 'Samochód został dodany.');
    }

    public function show(Car $car)
    {
        return redirect()->route('admin.cars.edit', $car);
    }

    public function edit(Car $car)
    {
        $car->load('damages', 'tireSets.tires', 'images', 'galleryImages', 'damageImages', 'pano360Image', 'exteriorPano360Image');
        $brands = Brand::orderBy('name')->get();

        $viewStats = [
            'total'    => $car->views()->count(),
            'today'    => $car->views()->whereDate('created_at', today())->count(),
            'last_7d'  => $car->views()->where('created_at', '>=', now()->subDays(7))->count(),
            'last_30d' => $car->views()->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $viewChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $viewChart[] = [
                'label' => $d->format('d.m'),
                'count' => $car->views()->whereDate('created_at', $d)->count(),
            ];
        }

        return view('admin.cars.edit', compact('car', 'brands', 'viewStats', 'viewChart'));
    }

    public function update(Request $request, Car $car)
    {
        $validated = $this->validateCar($request);
        $validated = $this->processEquipment($validated);
        unset($validated['engine_video_file'], $validated['remove_engine_video'], $validated['image_alt']);
        $car->update($validated);

        $this->handleEngineVideo($car, $request);
        $this->syncRelations($car, $request);
        $this->handleImages($car, $request);

        Cache::forget('catalog.filters');

        return redirect()->route('admin.cars.edit', $car)
            ->with('success', 'Samochód został zaktualizowany.');
    }

    private function handleEngineVideo(Car $car, Request $request): void
    {
        if ($request->boolean('remove_engine_video') && $car->engine_video_path && !str_starts_with($car->engine_video_path, 'http')) {
            Storage::disk('public')->delete($car->engine_video_path);
            $car->update(['engine_video_path' => null]);
        }

        if ($request->hasFile('engine_video_file')) {
            if ($car->engine_video_path && !str_starts_with($car->engine_video_path, 'http')) {
                Storage::disk('public')->delete($car->engine_video_path);
            }
            $path = $request->file('engine_video_file')->store('cars/' . $car->id . '/videos', 'public');
            $car->update(['engine_video_path' => $path]);
        }
    }

    public function destroy(Car $car)
    {
        foreach ($car->images as $image) {
            if (!str_starts_with($image->path, 'http')) {
                Storage::disk('public')->delete($image->path);
            }
        }
        foreach ($car->damages as $d) {
            if ($d->image_path && !str_starts_with($d->image_path, 'http')) {
                Storage::disk('public')->delete($d->image_path);
            }
        }
        if ($car->engine_video_path && !str_starts_with($car->engine_video_path, 'http')) {
            Storage::disk('public')->delete($car->engine_video_path);
        }
        $car->delete();

        Cache::forget('catalog.filters');

        return redirect()->route('admin.cars.index')
            ->with('success', 'Samochód został usunięty.');
    }

    public function toggleFeatured(Car $car)
    {
        $car->update(['is_featured' => !$car->is_featured]);
        return back()->with('success', 'Status wyróżnienia zmieniony.');
    }

    public function toggleSold(Car $car)
    {
        $car->update([
            'is_sold' => !$car->is_sold,
            'status' => !$car->is_sold ? 'sold' : 'active',
        ]);
        return back()->with('success', 'Status sprzedaży zmieniony.');
    }

    private function processEquipment(array $validated): array
    {
        if (!empty($validated['equipment'])) {
            $processed = [];
            foreach ($validated['equipment'] as $category => $items) {
                if (is_string($items)) {
                    $processed[$category] = array_values(array_filter(
                        array_map('trim', explode("\n", $items))
                    ));
                } else {
                    $processed[$category] = $items;
                }
            }
            $validated['equipment'] = $processed;
        }
        return $validated;
    }

    private function validateCar(Request $request): array
    {
        return $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:5',
            'price_type' => 'nullable|string|max:255',
            'taxation' => 'nullable|string|max:100',

            'seller_name' => 'nullable|string|max:255',
            'seller_phone' => 'nullable|string|max:50',
            'seller_email' => 'nullable|email|max:200',
            'commission_note' => 'nullable|string|max:1000',
            'reception_date' => 'nullable|date',

            'color' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|max:100',
            'doors' => 'nullable|string|max:10',
            'seats' => 'nullable|integer|min:1',
            'weight' => 'nullable|integer|min:0',
            'upholstery' => 'nullable|string|max:100',
            'vin' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:50',
            'first_registration' => 'nullable|string|max:20',
            'mileage' => 'nullable|integer|min:0',
            'previous_owners' => 'nullable|integer|min:0',
            'business_use' => 'nullable|string|max:100',
            'number_of_keys' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'power_hp' => 'nullable|integer|min:0',
            'power_kw' => 'nullable|integer|min:0',
            'engine_capacity' => 'nullable|integer|min:0',
            'transmission' => 'nullable|string|max:100',
            'transmission_detail' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'location_distance' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'is_imported' => 'nullable|boolean',
            'country_registration' => 'nullable|string|max:100',
            'last_service' => 'nullable|string|max:100',
            'last_service_mileage' => 'nullable|string|max:100',
            'next_inspection' => 'nullable|string|max:100',
            'service_documentation' => 'nullable|string|max:100',
            'fuel_consumption' => 'nullable|string|max:100',
            'fuel_procedure' => 'nullable|string|max:255',
            'co2_emission' => 'nullable|string|max:100',
            'emission_class' => 'nullable|string|max:50',
            'service_book' => 'nullable|string|max:50',
            'coc_documents' => 'nullable|string|max:10',
            'vehicle_folder' => 'nullable|string|max:10',
            'hu_au_report' => 'nullable|string|max:10',
            'engine_video_url' => 'nullable|url|max:500',
            'engine_video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:102400',
            'remove_engine_video' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_sold' => 'nullable|boolean',
            'has_certicheck' => 'nullable|boolean',
            'status' => 'nullable|string|in:draft,active,sold,reserved',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:320',
            'focus_keyword' => 'nullable|string|max:120',
            'noindex' => 'nullable|boolean',
            'image_alt' => 'nullable|array',
            'image_alt.*' => 'nullable|string|max:255',
            'gallery_images' => 'nullable|array|max:30',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:8192',
            'damage_images' => 'nullable|array|max:30',
            'damage_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:8192',
            'pano360_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
            'remove_pano360'   => 'nullable|boolean',
            'pano360ext_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
            'remove_pano360ext' => 'nullable|boolean',
            'damages.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:8192',
            'primary_image_id' => 'nullable|integer|exists:car_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:car_images,id',
            'image_order' => 'nullable|array',
            'image_order.*' => 'integer|exists:car_images,id',
            'paint_measurements' => 'nullable|array',
            'technical_conditions' => 'nullable|array',
            'equipment' => 'nullable|array',
        ]);
    }

    private function syncRelations(Car $car, Request $request): void
    {
        if ($request->has('damages')) {
            $oldDamages = $car->damages()->get()->keyBy(fn($d) => (string) $d->id);
            $keptIds = [];
            $uploads = $request->file('damages', []);

            foreach ($request->damages as $index => $damage) {
                if (empty($damage['area'])) continue;

                $existingId = $damage['id'] ?? null;
                $existing = $existingId ? $oldDamages->get((string) $existingId) : null;

                $attrs = [
                    'area'          => $damage['area'],
                    'severity'      => $damage['severity'] ?? 'warning',
                    'type'          => $damage['type'] ?? 'damage',
                    'tags'          => !empty($damage['tags']) ? array_filter(array_map('trim', explode(',', $damage['tags']))) : [],
                    'description'   => $damage['description'] ?? null,
                    'position_x'    => $damage['position_x'] ?? null,
                    'position_y'    => $damage['position_y'] ?? null,
                    'position_view' => in_array($damage['position_view'] ?? 'top', ['top', 'front', 'rear', 'left', 'right'], true) ? $damage['position_view'] : 'top',
                    'image_path'    => $existing?->image_path,
                ];

                if (!empty($damage['remove_image']) && $existing?->image_path && !str_starts_with($existing->image_path, 'http')) {
                    Storage::disk('public')->delete($existing->image_path);
                    $attrs['image_path'] = null;
                }

                if (isset($uploads[$index]['image']) && $uploads[$index]['image']) {
                    if ($existing?->image_path && !str_starts_with($existing->image_path, 'http')) {
                        Storage::disk('public')->delete($existing->image_path);
                    }
                    $attrs['image_path'] = $uploads[$index]['image']->store('cars/' . $car->id . '/damages', 'public');
                }

                if ($existing) {
                    $existing->update($attrs);
                    $keptIds[] = $existing->id;
                } else {
                    $new = $car->damages()->create($attrs);
                    $keptIds[] = $new->id;
                }
            }

            $toDelete = $oldDamages->whereNotIn('id', $keptIds);
            foreach ($toDelete as $d) {
                if ($d->image_path && !str_starts_with($d->image_path, 'http')) {
                    Storage::disk('public')->delete($d->image_path);
                }
                $d->delete();
            }
        }

        if ($request->has('tire_sets')) {
            $car->tireSets()->delete();
            foreach ($request->tire_sets as $setData) {
                if (!empty($setData['tire_type'])) {
                    $set = $car->tireSets()->create([
                        'set_number' => $setData['set_number'] ?? 1,
                        'is_mounted' => !empty($setData['is_mounted']),
                        'tire_type' => $setData['tire_type'],
                        'rim' => $setData['rim'] ?? null,
                        'notes' => $setData['notes'] ?? null,
                    ]);

                    if (!empty($setData['tires'])) {
                        foreach ($setData['tires'] as $tireData) {
                            if (!empty($tireData['position'])) {
                                $set->tires()->create([
                                    'position' => $tireData['position'],
                                    'tread_depth' => $tireData['tread_depth'] ?? null,
                                    'condition' => !empty($tireData['condition'])
                                        ? array_filter(array_map('trim', explode(',', $tireData['condition'])))
                                        : [],
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function handleImages(Car $car, Request $request): void
    {
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $file->store('cars/' . $car->id . '/gallery', 'public');
                $this->optimizeImage($path, 1920);
                $car->images()->create([
                    'path' => $path,
                    'type' => 'gallery',
                    'is_primary' => $car->images()->count() === 0 && $index === 0,
                    'sort_order' => $car->images()->max('sort_order') + 1,
                ]);
            }
        }

        if ($request->hasFile('damage_images')) {
            foreach ($request->file('damage_images') as $file) {
                $path = $file->store('cars/' . $car->id . '/damage', 'public');
                $this->optimizeImage($path, 1280);
                $car->images()->create([
                    'path' => $path,
                    'type' => 'damage',
                    'sort_order' => $car->images()->max('sort_order') + 1,
                ]);
            }
        }

        if ($request->filled('delete_images')) {
            $imagesToDelete = $car->images()->whereIn('id', $request->delete_images)->get();
            foreach ($imagesToDelete as $img) {
                if (!str_starts_with($img->path, 'http')) {
                    Storage::disk('public')->delete($img->path);
                }
                $img->delete();
            }
        }

        if ($request->filled('primary_image_id')) {
            $car->images()->update(['is_primary' => false]);
            $car->images()->where('id', $request->primary_image_id)->update(['is_primary' => true]);
        }

        if ($request->filled('image_order') && is_array($request->image_order)) {
            foreach ($request->image_order as $position => $imageId) {
                $car->images()->where('id', (int) $imageId)->update(['sort_order' => $position]);
            }
        }

        if ($request->has('image_alt') && is_array($request->image_alt)) {
            foreach ($request->image_alt as $imgId => $alt) {
                $clean = trim((string) $alt);
                $car->images()->where('id', (int) $imgId)->update(['alt_text' => $clean !== '' ? $clean : null]);
            }
        }

        // ===== 360° panorama interior (single equirectangular image) =====
        if ($request->boolean('remove_pano360') && $car->pano360Image) {
            if (!str_starts_with($car->pano360Image->path, 'http')) {
                Storage::disk('public')->delete($car->pano360Image->path);
            }
            $car->pano360Image->delete();
        }

        if ($request->hasFile('pano360_image')) {
            // Replace existing if any.
            foreach ($car->pano360Image()->get() as $old) {
                if (!str_starts_with($old->path, 'http')) {
                    Storage::disk('public')->delete($old->path);
                }
                $old->delete();
            }
            $path = $request->file('pano360_image')->store('cars/' . $car->id . '/pano360', 'public');
            $car->images()->create([
                'path'       => $path,
                'type'       => 'pano360',
                'sort_order' => 0,
            ]);
        }

        // ===== 360° panorama exterior =====
        if ($request->boolean('remove_pano360ext') && $car->exteriorPano360Image) {
            if (!str_starts_with($car->exteriorPano360Image->path, 'http')) {
                Storage::disk('public')->delete($car->exteriorPano360Image->path);
            }
            $car->exteriorPano360Image->delete();
        }

        if ($request->hasFile('pano360ext_image')) {
            foreach ($car->exteriorPano360Image()->get() as $old) {
                if (!str_starts_with($old->path, 'http')) {
                    Storage::disk('public')->delete($old->path);
                }
                $old->delete();
            }
            $path = $request->file('pano360ext_image')->store('cars/' . $car->id . '/pano360ext', 'public');
            $car->images()->create([
                'path'       => $path,
                'type'       => 'pano360ext',
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Resize and re-compress an image stored on the public disk using PHP GD.
     * Skips if GD is not available, file is missing, or width is already within limit.
     */
    private function optimizeImage(string $storedPath, int $maxWidth): void
    {
        if (!function_exists('imagecreatefromjpeg')) return;

        $fullPath = Storage::disk('public')->path($storedPath);
        if (!file_exists($fullPath)) return;

        $info = @getimagesize($fullPath);
        if (!$info || $info[0] <= $maxWidth) return;

        [$width, $height, $type] = $info;

        try {
            $src = match ($type) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($fullPath),
                IMAGETYPE_PNG  => imagecreatefrompng($fullPath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($fullPath) : null,
                default        => null,
            };

            if (!$src) return;

            $newHeight = (int) round($height * $maxWidth / $width);
            $dst = imagescale($src, $maxWidth, $newHeight, IMG_BICUBIC);

            imagejpeg($dst, $fullPath, 85);

            imagedestroy($src);
            imagedestroy($dst);
        } catch (\Throwable) {
            // Optimization failed — original file kept as-is.
        }
    }
}
