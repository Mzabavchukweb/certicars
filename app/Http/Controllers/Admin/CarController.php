<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        Cache::forget('sitemap.xml');
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
            foreach ($car->damages as $d) {
                if ($d->image_path && !str_starts_with($d->image_path, 'http')) {
                    Storage::disk('public')->delete($d->image_path);
                }
            }
            if ($car->engine_video_path && !str_starts_with($car->engine_video_path, 'http')) {
                Storage::disk('public')->delete($car->engine_video_path);
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
        $reqId   = (string) \Illuminate\Support\Str::uuid();
        $userId  = optional($request->user())->id;
        $fileCounts = $this->safeFileCounts($request);
        $diag = $this->parseClientDiag($request);
        $totalStart = microtime(true);
        // Make timing context visible to handleImages / handleEngineVideo
        // for the car.save.phase log lines.
        $request->attributes->set('_save_rid', $reqId);
        $request->attributes->set('_save_op', 'store');
        \Log::info('car.save.start', [
            'rid'         => $reqId,
            'op'          => 'store',
            'user_id'     => $userId,
            'files'       => $fileCounts,
            'req_bytes'   => (int) $request->server('CONTENT_LENGTH'),
            'field_count' => count($request->all()),
            'button'      => $diag['button'] ?? null,
            'step'        => $diag['step'] ?? null,
            'opened_sec'  => $diag['elapsed_sec'] ?? null,
            'had_draft'   => $diag['had_draft'] ?? null,
        ]);

        try {
            $validated = $this->validateCar($request);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \Log::warning('car.save.validation_failed', [
                'rid' => $reqId, 'op' => 'store', 'user_id' => $userId,
                'fields' => array_keys($ve->errors()),
            ]);
            throw $ve;
        }
        $validated = $this->processEquipment($validated);
        unset($validated['engine_video_file'], $validated['remove_engine_video'], $validated['image_alt'], $validated['active_tab']);

        try {
            // Phase 1: DB writes inside a transaction. If anything here throws,
            // NO partial car/relations remain in DB.
            $car = DB::transaction(function () use ($validated, $request) {
                $newCar = Car::create($validated);
                $this->handleEngineVideo($newCar, $request);
                $this->syncRelations($newCar, $request);
                return $newCar;
            });
        } catch (\Throwable $e) {
            \Log::error('car.save.db_failed', [
                'rid' => $reqId, 'op' => 'store', 'user_id' => $userId,
                'exception' => get_class($e), 'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Nie udało się zapisać samochodu. Spróbuj ponownie, a jeśli problem się powtórzy — skontaktuj się z administratorem.');
        }

        // Phase 2: Image uploads — outside transaction (filesystem ops can't roll back).
        $imageFailures = [];
        try {
            $imageFailures = $this->handleImages($car, $request);
        } catch (\Throwable $e) {
            \Log::error('car.save.image_phase_failed', [
                'rid' => $reqId, 'op' => 'store', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
            ]);
            $imageFailures[] = '(błąd przesyłania)';
        }

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        \Log::info('car.save.success', [
            'rid' => $reqId, 'op' => 'store', 'user_id' => $userId, 'car_id' => $car->id,
            'image_failures' => count($imageFailures),
            'total_ms' => (int) round((microtime(true) - $totalStart) * 1000),
        ]);

        $redirect = redirect($this->editUrlWithTab($car, $request));
        if (!empty($imageFailures)) {
            return $redirect
                ->with('success', 'Samochód został dodany.')
                ->with('warning', $this->formatImageFailureMessage($imageFailures));
        }
        return $redirect->with('success', 'Samochód został dodany.');
    }

    public function show(Car $car)
    {
        return redirect()->route('admin.cars.edit', $car);
    }

    public function edit(Car $car)
    {
        $car->load('damages.photos', 'tireSets.tires', 'images', 'galleryImages', 'damageImages', 'pano360Image', 'exteriorPano360Image');
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
        $reqId   = (string) \Illuminate\Support\Str::uuid();
        $userId  = optional($request->user())->id;
        $fileCounts = $this->safeFileCounts($request);
        $diag = $this->parseClientDiag($request);
        $totalStart = microtime(true);
        $request->attributes->set('_save_rid', $reqId);
        $request->attributes->set('_save_op', 'update');
        \Log::info('car.save.start', [
            'rid'         => $reqId,
            'op'          => 'update',
            'user_id'     => $userId,
            'car_id'      => $car->id,
            'files'       => $fileCounts,
            'req_bytes'   => (int) $request->server('CONTENT_LENGTH'),
            'field_count' => count($request->all()),
            'button'      => $diag['button'] ?? null,
            'step'        => $diag['step'] ?? null,
            'opened_sec'  => $diag['elapsed_sec'] ?? null,
            'had_draft'   => $diag['had_draft'] ?? null,
        ]);

        try {
            $validated = $this->validateCar($request);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \Log::warning('car.save.validation_failed', [
                'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
                'fields' => array_keys($ve->errors()),
            ]);
            throw $ve;
        }
        $validated = $this->processEquipment($validated);
        unset($validated['engine_video_file'], $validated['remove_engine_video'], $validated['image_alt'], $validated['active_tab']);

        try {
            // DB writes wrapped — if relations sync fails, the car update + relations roll back.
            DB::transaction(function () use ($car, $validated, $request) {
                $car->update($validated);
                $this->handleEngineVideo($car, $request);
                $this->syncRelations($car, $request);
            });
        } catch (\Throwable $e) {
            \Log::error('car.save.db_failed', [
                'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Nie udało się zaktualizować samochodu. Twoje zmiany nie zostały zapisane — spróbuj ponownie.');
        }

        $imageFailures = [];
        try {
            $imageFailures = $this->handleImages($car, $request);
        } catch (\Throwable $e) {
            \Log::error('car.save.image_phase_failed', [
                'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
            ]);
            $imageFailures[] = '(błąd przesyłania)';
        }

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        \Log::info('car.save.success', [
            'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
            'image_failures' => count($imageFailures),
            'total_ms' => (int) round((microtime(true) - $totalStart) * 1000),
        ]);

        $redirect = redirect($this->editUrlWithTab($car, $request));
        if (!empty($imageFailures)) {
            return $redirect
                ->with('success', 'Samochód został zaktualizowany.')
                ->with('warning', $this->formatImageFailureMessage($imageFailures));
        }
        return $redirect->with('success', 'Samochód został zaktualizowany.');
    }

    /**
     * Count uploaded files per field — safe to log (no contents, no PII).
     */
    private function safeFileCounts(Request $request): array
    {
        $out = [];
        foreach (['gallery_images', 'damage_images', 'pano360_image', 'pano360ext_image', 'engine_video_file'] as $field) {
            if ($request->hasFile($field)) {
                $val = $request->file($field);
                $out[$field] = is_array($val) ? count($val) : 1;
            }
        }
        return $out;
    }

    /**
     * Parse the wizard's client-side `_diag` hidden input. Safe to log:
     * only integers + a button-name enum + a step number. Never contains
     * PII, passwords, secrets, or raw payload. Returns [] on any parse error
     * (the diag is best-effort observability, not load-bearing).
     */
    private function parseClientDiag(Request $request): array
    {
        $raw = $request->input('_diag');
        if (!is_string($raw) || $raw === '') return [];
        $data = json_decode($raw, true);
        if (!is_array($data)) return [];
        $allowedKeys = ['opened_at', 'elapsed_sec', 'field_count', 'file_count', 'file_total_bytes', 'button', 'step', 'had_draft'];
        $out = [];
        foreach ($allowedKeys as $k) {
            if (!array_key_exists($k, $data)) continue;
            $v = $data[$k];
            if ($k === 'button') {
                $out[$k] = in_array($v, ['save', 'save_exit'], true) ? $v : null;
            } elseif ($k === 'had_draft') {
                $out[$k] = (bool) $v;
            } elseif (is_numeric($v)) {
                $out[$k] = (int) $v;
            }
        }
        return $out;
    }

    /**
     * Build a Polish flash message listing image upload failures.
     * Caps the listed names at 5 and adds an "and N more" suffix.
     */
    private function formatImageFailureMessage(array $failures): string
    {
        $unique = array_values(array_unique(array_filter($failures, fn($n) => $n !== '')));
        $count  = count($unique);
        if ($count === 0) return '';
        $listed = array_slice($unique, 0, 5);
        $msg    = 'Niektóre zdjęcia nie zostały wgrane: ' . implode(', ', $listed);
        if ($count > 5) {
            $msg .= ' (i ' . ($count - 5) . ' więcej)';
        }
        $msg .= '. Spróbuj wgrać je ponownie w zakładce „Zdjęcia".';
        return $msg;
    }

    /**
     * Wraps $file->store() with explicit return-value validation. The S3/R2 disk
     * has 'throw' => false, so a failed PUT returns false instead of throwing —
     * which would otherwise create CarImage rows pointing at `false`.
     *
     * Returns the stored path on success, or null on any failure (with logged context).
     */
    private function safeStore($file, string $directory): ?string
    {
        try {
            $path = $file->store($directory, 'public');
            if (!is_string($path) || $path === '') {
                \Log::warning('Image store returned non-path', [
                    'directory' => $directory,
                    'original'  => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
                    'returned'  => var_export($path, true),
                ]);
                return null;
            }
            return $path;
        } catch (\Throwable $e) {
            \Log::error('Image store threw', [
                'directory' => $directory,
                'original'  => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function editUrlWithTab(Car $car, Request $request): string
    {
        $url = route('admin.cars.edit', $car);
        $tab = $request->input('active_tab');
        $allowedTabs = ['basic','engine','vehicle','service','seller','equipment','condition','damages','tires','images','seo'];

        if (is_string($tab) && in_array($tab, $allowedTabs, true)) {
            $url .= '#' . $tab;
        }

        return $url;
    }

    private function handleEngineVideo(Car $car, Request $request): void
    {
        // Explicit "remove" flag — keep deleting the old file here. Safe: this
        // path only runs when the operator explicitly checked Usuń nagranie.
        if ($request->boolean('remove_engine_video') && $car->engine_video_path && !str_starts_with($car->engine_video_path, 'http')) {
            Storage::disk('public')->delete($car->engine_video_path);
            $car->update(['engine_video_path' => null]);
        }

        if ($request->hasFile('engine_video_file')) {
            $videoFile = $request->file('engine_video_file');
            $rid = (string) ($request->attributes->get('_save_rid') ?? '');
            $op  = (string) ($request->attributes->get('_save_op') ?? 'store');
            // Upload-first, delete-old-after. The pre-fix order deleted the
            // existing video BEFORE storing the new one — a failed R2 upload
            // then destroyed both the new and the old. safeStore() validates
            // the Storage::put return value so a silent R2 false-return
            // doesn't leave the DB pointing at a broken path.
            $newPath = $this->timePhase($rid, $op, $car->id, 'engine_video_upload',
                fn() => $this->safeStore($videoFile, 'cars/' . $car->id . '/videos'),
                ['size_mb' => (int) round(($videoFile->getSize() ?: 0) / 1024 / 1024)]
            );
            if ($newPath === null) {
                // Upload failed — preserve the existing engine_video_path
                // (don't overwrite, don't delete) and surface a clear warning
                // via the caller's image-failure flash.
                \Log::warning('car.save.engine_video_upload_failed', [
                    'car_id'   => $car->id,
                    'original' => $videoFile->getClientOriginalName(),
                    'size'     => $videoFile->getSize(),
                    'mime'     => $videoFile->getMimeType(),
                ]);
                // Re-throw via the standard image-failure channel by adding
                // to the request so handleImages() can include it.
                $request->attributes->set('_engine_video_upload_failed', $videoFile->getClientOriginalName());
                return;
            }
            // Upload succeeded — NOW it's safe to delete the previous local file.
            $oldPath = $car->engine_video_path;
            $car->update(['engine_video_path' => $newPath]);
            if ($oldPath && !str_starts_with($oldPath, 'http') && $oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
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
        Cache::forget('sitemap.xml');

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

    public function uploadImage(Request $request, Car $car)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,avif|max:20480',
            'type'  => 'required|in:gallery,damage',
        ]);

        $type = $request->input('type', 'gallery');
        $subdir = $type === 'damage' ? 'damage' : 'gallery';

        Storage::disk('public')->makeDirectory('cars/' . $car->id . '/' . $subdir);
        // Same fast path as the form save flow: optimize the local temp file
        // before uploading. Avoids the R2 GET + PUT round-trip.
        $imageFile = $request->file('image');
        $this->optimizeUploadedFileInPlace($imageFile, $type === 'damage' ? 1280 : 1920);
        $path = $imageFile->store('cars/' . $car->id . '/' . $subdir, 'public');

        $img = $car->images()->create([
            'path'       => $path,
            'type'       => $type,
            'is_primary' => $car->images()->count() === 0,
            'sort_order' => ($car->images()->max('sort_order') ?? 0) + 1,
        ]);

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        return response()->json([
            'success' => true,
            'image'   => [
                'id'   => $img->id,
                'url'  => $img->url,
                'alt'  => $img->alt,
                'type' => $img->type,
            ],
        ]);
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

        // P1: technical_conditions must be a flat key=>string map for the wizard
        // form to render. Historical data and external imports (Otomoto, manual
        // SQL) have produced nested {"engine":{"status":"OK"}} shapes that crash
        // the edit form with htmlspecialchars(array). Normalize on every save.
        if (!empty($validated['technical_conditions']) && is_array($validated['technical_conditions'])) {
            $normalized = [];
            foreach ($validated['technical_conditions'] as $key => $value) {
                if (is_string($value)) {
                    $normalized[$key] = $value;
                } elseif (is_scalar($value)) {
                    $normalized[$key] = (string) $value;
                } elseif (is_array($value)) {
                    foreach (['status', 'notes', 'value', 'text', 'description', 0] as $k) {
                        if (array_key_exists($k, $value) && is_scalar($value[$k])) {
                            $normalized[$key] = (string) $value[$k];
                            continue 2;
                        }
                    }
                    $flat = array_filter($value, 'is_scalar');
                    $normalized[$key] = $flat ? implode(' · ', array_map('strval', $flat)) : '';
                }
            }
            $validated['technical_conditions'] = $normalized;
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
            'previous_owners' => 'nullable|string|max:50',
            'business_use' => 'nullable|string|max:100',
            'number_of_keys' => 'nullable|string|max:10',
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
            'imported_from' => 'nullable|string|max:100',
            'vehicle_history' => 'nullable|string|max:100',
            'last_service' => 'nullable|string|max:100',
            'last_service_mileage' => 'nullable|string|max:100',
            'next_inspection' => 'nullable|string|max:100',
            'service_documentation' => 'nullable|string|max:100',
            'fuel_consumption' => 'nullable|string|max:100',
            'fuel_procedure' => 'nullable|string|max:255',
            'co2_emission' => 'nullable|string|max:100',
            'emission_class' => 'nullable|string|max:50',
            'service_book' => 'nullable|string|max:100',
            'coc_documents' => 'nullable|string|max:100',
            'vehicle_folder' => 'nullable|string|max:100',
            'hu_au_report' => 'nullable|string|max:100',
            'service_book_status' => 'nullable|string|max:100',
            'registration_cert' => 'nullable|string|max:100',
            'owners_manual' => 'nullable|string|max:100',
            'aso_serviced' => 'nullable|string|max:100',
            'service_history' => 'nullable|string|max:100',
            'engine_video_url' => 'nullable|url|max:500',
            'engine_video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:102400',
            'remove_engine_video' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_sold' => 'nullable|boolean',
            'has_certicheck' => 'nullable|boolean',
            'available_now' => 'nullable|boolean',
            'home_delivery' => 'nullable|boolean',
            'has_gethelp' => 'nullable|boolean',
            'gethelp_package' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:draft,active,sold,reserved',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:320',
            'focus_keyword' => 'nullable|string|max:120',
            'noindex' => 'nullable|boolean',
            'image_alt' => 'nullable|array',
            'image_alt.*' => 'nullable|string|max:255',
            'gallery_images' => 'nullable|array|max:40',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:20480',
            'damage_images' => 'nullable|array|max:40',
            'damage_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:20480',
            'pano360_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:25600',
            'remove_pano360'   => 'nullable|boolean',
            'pano360ext_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:25600',
            'remove_pano360ext' => 'nullable|boolean',
            'damages.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:20480',
            'damages.*.images' => 'nullable|array|max:20',
            'damages.*.images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:20480',
            'primary_image_id' => 'nullable|integer|exists:car_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:car_images,id',
            'image_order' => 'nullable|array',
            'image_order.*' => 'integer|exists:car_images,id',
            'active_tab' => 'nullable|string|max:32',
            'paint_measurements' => 'nullable|array',
            'technical_conditions' => 'nullable|array',
            'equipment' => 'nullable|array',
        ], [
            'brand_id.required' => 'Wybierz markę pojazdu.',
            'brand_id.exists'   => 'Wybrana marka nie istnieje w systemie.',
            'model.required'    => 'Podaj model pojazdu.',
            'model.max'         => 'Model pojazdu może mieć maksymalnie 255 znaków.',
            // Engine video — clear Polish error instead of empty-form reset.
            'engine_video_file.file'       => 'Nagranie pracy silnika musi być prawidłowym plikiem wideo.',
            'engine_video_file.mimetypes'  => 'Nieobsługiwany format wideo. Dozwolone: MP4, WebM, MOV (QuickTime), AVI, MKV.',
            'engine_video_file.max'        => 'Nagranie pracy silnika jest za duże. Maksymalny rozmiar to 100 MB.',
            'engine_video_file.uploaded'   => 'Nagranie pracy silnika jest za duże lub przesyłanie zostało przerwane. Maksymalny rozmiar to 100 MB.',
            'engine_video_url.url'         => 'Podaj prawidłowy adres URL nagrania pracy silnika.',
            'engine_video_url.max'         => 'Adres nagrania pracy silnika jest za długi (maks. 500 znaków).',
            // Image upload size hits — Laravel emits "uploaded" when file size is
            // accepted by PHP but exceeds the rule's max:* (KB) value.
            'gallery_images.*.max'          => 'Zdjęcie galerii jest za duże. Maksymalny rozmiar pojedynczego zdjęcia to 20 MB.',
            'gallery_images.*.uploaded'     => 'Zdjęcie galerii jest za duże lub przesyłanie zostało przerwane. Maksymalny rozmiar to 20 MB.',
            'gallery_images.*.mimes'        => 'Nieobsługiwany format zdjęcia. Dozwolone: JPG, PNG, WebP, AVIF.',
            'damage_images.*.max'           => 'Zdjęcie uszkodzenia jest za duże. Maksymalny rozmiar to 20 MB.',
            'damages.*.image.max'           => 'Zdjęcie uszkodzenia jest za duże. Maksymalny rozmiar to 20 MB.',
            'damages.*.images.*.max'        => 'Zdjęcie uszkodzenia jest za duże. Maksymalny rozmiar to 20 MB.',
            'pano360_image.max'             => 'Zdjęcie panoramy 360° wnętrza jest za duże. Maksymalny rozmiar to 25 MB.',
            'pano360ext_image.max'          => 'Zdjęcie panoramy 360° zewnętrza jest za duże. Maksymalny rozmiar to 25 MB.',
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

                // P1 ROOT-CAUSE FIX: the prior ternary read $damage['position_view']
                // in its true-branch without `??` protection. When position_view was
                // missing from the payload (any damage row added by the wizard before
                // the user clicked the body diagram, or older imported damage data),
                // `in_array('top', [...])` returned true → true-branch evaluated the
                // missing key → ErrorException → DB::transaction rolled back → user
                // saw the generic "Nie udało się zapisać samochodu" error and
                // believed nothing saved. Resolve by reading into a local first.
                $positionView = $damage['position_view'] ?? 'top';
                if (!in_array($positionView, ['top', 'front', 'rear', 'left', 'right'], true)) {
                    $positionView = 'top';
                }

                $attrs = [
                    'area'          => $damage['area'],
                    'severity'      => $damage['severity'] ?? 'warning',
                    'type'          => $damage['type'] ?? 'damage',
                    'tags'          => !empty($damage['tags']) ? array_filter(array_map('trim', explode(',', $damage['tags']))) : [],
                    'description'   => $damage['description'] ?? null,
                    'position_x'    => $damage['position_x'] ?? null,
                    'position_y'    => $damage['position_y'] ?? null,
                    'position_view' => $positionView,
                    'image_path'    => $existing?->image_path,
                ];

                // Remove main image
                if (!empty($damage['remove_image']) && $existing?->image_path && !str_starts_with($existing->image_path, 'http')) {
                    Storage::disk('public')->delete($existing->image_path);
                    $attrs['image_path'] = null;
                }

                // Single image upload (backward compat). safeStore() validates the
                // ->store() return value so a failed R2 PUT can't leave the DB
                // pointing at "false" — that would render as a 404 thumbnail
                // forever.
                if (isset($uploads[$index]['image']) && $uploads[$index]['image']) {
                    Storage::disk('public')->makeDirectory('cars/' . $car->id . '/damages');
                    $newImgPath = $this->safeStore($uploads[$index]['image'], 'cars/' . $car->id . '/damages');
                    if ($newImgPath !== null) {
                        // Upload-first, delete-old-after.
                        $oldImgPath = $existing?->image_path;
                        $attrs['image_path'] = $newImgPath;
                        if ($oldImgPath && !str_starts_with($oldImgPath, 'http') && $oldImgPath !== $newImgPath) {
                            Storage::disk('public')->delete($oldImgPath);
                        }
                    }
                    // If safeStore returned null: keep $attrs['image_path'] as it
                    // was set earlier (existing path preserved) and let the
                    // operator retry. handleImages collects the failure flash.
                }

                if ($existing) {
                    $existing->update($attrs);
                    $dmgRecord = $existing;
                    $keptIds[] = $existing->id;
                } else {
                    $dmgRecord = $car->damages()->create($attrs);
                    $keptIds[] = $dmgRecord->id;
                }

                // Remove selected photos
                if (!empty($damage['remove_photos'])) {
                    $photosToRemove = \App\Models\CarImage::whereIn('id', $damage['remove_photos'])->where('damage_id', $dmgRecord->id)->get();
                    foreach ($photosToRemove as $p) {
                        if ($p->path && !str_starts_with($p->path, 'http')) {
                            Storage::disk('public')->delete($p->path);
                        }
                        $p->delete();
                    }
                }

                // Multi-image uploads. safeStore() prevents CarImage rows with
                // path=false on R2 PUT failure (would render as broken 404
                // thumbnails on the public page forever).
                if (isset($uploads[$index]['images']) && is_array($uploads[$index]['images'])) {
                    Storage::disk('public')->makeDirectory('cars/' . $car->id . '/damages');
                    $sortOrder = $dmgRecord->photos()->max('sort_order') ?? 0;
                    foreach ($uploads[$index]['images'] as $imgFile) {
                        if ($imgFile && $imgFile->isValid()) {
                            $path = $this->safeStore($imgFile, 'cars/' . $car->id . '/damages');
                            if ($path === null) {
                                // Storage failed — DO NOT create CarImage row. Operator
                                // will see the upload-failed flash via handleImages.
                                continue;
                            }
                            \App\Models\CarImage::create([
                                'car_id'     => $car->id,
                                'damage_id'  => $dmgRecord->id,
                                'path'       => $path,
                                'type'       => 'damage',
                                'sort_order' => ++$sortOrder,
                            ]);
                        }
                    }
                }
            }

            $toDelete = $oldDamages->whereNotIn('id', $keptIds);
            foreach ($toDelete as $d) {
                if ($d->image_path && !str_starts_with($d->image_path, 'http')) {
                    Storage::disk('public')->delete($d->image_path);
                }
                // Delete associated photos
                foreach ($d->photos as $p) {
                    if ($p->path && !str_starts_with($p->path, 'http')) {
                        Storage::disk('public')->delete($p->path);
                    }
                    $p->delete();
                }
                $d->delete();
            }
        }

        if ($request->has('tire_sets')) {
            DB::transaction(function () use ($car, $request) {
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
            });
        }
    }

    /**
     * Handle gallery / damage / pano image uploads.
     * Returns an array of failed file names (empty when all uploads succeeded).
     * Never throws — individual upload failures are logged and reported,
     * but never fail the whole save (the car is already in DB by this point).
     */
    private function handleImages(Car $car, Request $request): array
    {
        $failures = [];

        // Pick up an engine-video upload failure from handleEngineVideo() so the
        // operator gets a single combined "these files didn't upload" toast
        // instead of a silent partial save.
        if ($videoFailure = $request->attributes->get('_engine_video_upload_failed')) {
            $failures[] = $videoFailure . ' (nagranie pracy silnika)';
        }

        // Ensure upload directories exist
        $basePath = 'cars/' . $car->id;
        foreach (['gallery', 'damage', 'pano360', 'pano360ext'] as $sub) {
            Storage::disk('public')->makeDirectory($basePath . '/' . $sub);
        }

        // Phase-timing context — set by store()/update() before calling handleImages.
        $rid = (string) ($request->attributes->get('_save_rid') ?? '');
        $op  = (string) ($request->attributes->get('_save_op') ?? 'store');

        if ($request->hasFile('gallery_images')) {
            $galleryFiles = $request->file('gallery_images');
            $this->timePhase($rid, $op, $car->id, 'gallery_upload', function () use ($car, $galleryFiles, &$failures) {
                foreach ($galleryFiles as $index => $file) {
                    // FAST PATH: optimize the local temp file IN PLACE before
                    // shipping it to R2. Pre-fix code uploaded the original,
                    // then downloaded + re-encoded + re-uploaded — 3× R2
                    // round-trips per image. This refactor cuts to 1× PUT.
                    $this->optimizeUploadedFileInPlace($file, 1920);
                    $path = $this->safeStore($file, 'cars/' . $car->id . '/gallery');
                    if ($path === null) {
                        $failures[] = $file->getClientOriginalName();
                        continue;
                    }
                    $car->images()->create([
                        'path' => $path,
                        'type' => 'gallery',
                        'is_primary' => $car->images()->count() === 0 && $index === 0,
                        'sort_order' => $car->images()->max('sort_order') + 1,
                    ]);
                }
            }, ['count' => count($galleryFiles)]);
        }

        if ($request->hasFile('damage_images')) {
            $damageFiles = $request->file('damage_images');
            $this->timePhase($rid, $op, $car->id, 'damage_upload', function () use ($car, $damageFiles, &$failures) {
                foreach ($damageFiles as $file) {
                    $this->optimizeUploadedFileInPlace($file, 1280);
                    $path = $this->safeStore($file, 'cars/' . $car->id . '/damage');
                    if ($path === null) {
                        $failures[] = $file->getClientOriginalName();
                        continue;
                    }
                    $car->images()->create([
                        'path' => $path,
                        'type' => 'damage',
                        'sort_order' => $car->images()->max('sort_order') + 1,
                    ]);
                }
            }, ['count' => count($damageFiles)]);
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
            $panoFile = $request->file('pano360_image');
            $this->timePhase($rid, $op, $car->id, 'pano360_upload', function () use ($car, $panoFile, &$failures) {
                $path = $this->safeStore($panoFile, 'cars/' . $car->id . '/pano360');
                if ($path === null) {
                    $failures[] = $panoFile->getClientOriginalName();
                    return;
                }
                // Replace existing only AFTER the new upload succeeded — never delete the old one on a failed re-upload.
                foreach ($car->pano360Image()->get() as $old) {
                    if (!str_starts_with($old->path, 'http')) {
                        Storage::disk('public')->delete($old->path);
                    }
                    $old->delete();
                }
                $car->images()->create([
                    'path'       => $path,
                    'type'       => 'pano360',
                    'sort_order' => 0,
                ]);
            });
        }

        // ===== 360° panorama exterior =====
        if ($request->boolean('remove_pano360ext') && $car->exteriorPano360Image) {
            if (!str_starts_with($car->exteriorPano360Image->path, 'http')) {
                Storage::disk('public')->delete($car->exteriorPano360Image->path);
            }
            $car->exteriorPano360Image->delete();
        }

        if ($request->hasFile('pano360ext_image')) {
            $panoExtFile = $request->file('pano360ext_image');
            $this->timePhase($rid, $op, $car->id, 'pano360ext_upload', function () use ($car, $panoExtFile, &$failures) {
                $path = $this->safeStore($panoExtFile, 'cars/' . $car->id . '/pano360ext');
                if ($path === null) {
                    $failures[] = $panoExtFile->getClientOriginalName();
                    return;
                }
                foreach ($car->exteriorPano360Image()->get() as $old) {
                    if (!str_starts_with($old->path, 'http')) {
                        Storage::disk('public')->delete($old->path);
                    }
                    $old->delete();
                }
                $car->images()->create([
                    'path'       => $path,
                    'type'       => 'pano360ext',
                    'sort_order' => 0,
                ]);
            });
        }

        return $failures;
    }

    /**
     * Resize and re-compress an UploadedFile IN PLACE before it gets uploaded
     * to R2/disk. This is the fast path — the GD work happens on PHP's local
     * temp upload file (already on the same machine, no network), then a
     * single PUT sends the optimized result to R2.
     *
     * Pre-fix code (`optimizeImage` below) ran AFTER the upload: it downloaded
     * the file back from R2, re-encoded, and re-uploaded — three R2
     * round-trips per image. For 15-photo saves this was the dominant cost
     * (~75s of the 3–4 minute total). This refactor cuts it to ~0s.
     *
     * If GD is unavailable, the file is not a JPEG, or width is already
     * within limit, the original file is left untouched and uploaded as-is.
     * Same final output as the post-upload path, just without the round-trip.
     */
    private function optimizeUploadedFileInPlace($file, int $maxWidth): void
    {
        if (!function_exists('imagecreatefromjpeg')) return;
        if (!$file || !method_exists($file, 'getRealPath')) return;
        $fullPath = $file->getRealPath();
        if (!is_string($fullPath) || !is_file($fullPath)) return;

        $info = @getimagesize($fullPath);
        if (!$info || $info[0] <= $maxWidth) return;
        [$width, $height, $type] = $info;

        // Only recompress JPEG — PNG/WebP would be silently reformatted to
        // JPEG while keeping the original extension, causing browser decode
        // errors (preserved from the old optimizeImage logic).
        if ($type !== IMAGETYPE_JPEG) return;

        try {
            $src = imagecreatefromjpeg($fullPath);
            if (!$src) return;

            $newHeight = (int) round($height * $maxWidth / $width);
            $dst = imagescale($src, $maxWidth, $newHeight, IMG_BICUBIC);
            $ok  = imagejpeg($dst, $fullPath, 85);
            imagedestroy($src);
            imagedestroy($dst);

            if (!$ok) return;
            clearstatcache(true, $fullPath); // refresh file size cache so $file->getSize() is accurate
        } catch (\Throwable) {
            // Optimization failed — leave the original file in place; it
            // will be uploaded as-is. No partial-write risk because GD's
            // imagejpeg returns false instead of truncating on failure.
        }
    }

    /**
     * Time a phase + log it under car.save.phase. Safe context only —
     * never logs payload, secrets, or PII. Skips logging on negligible
     * (<5ms) phases to keep the log volume sane.
     */
    private function timePhase(string $rid, string $op, ?int $carId, string $phase, callable $fn, array $extra = []): mixed
    {
        $start = microtime(true);
        $result = $fn();
        $durationMs = (int) round((microtime(true) - $start) * 1000);
        if ($durationMs >= 5) {
            \Log::info('car.save.phase', array_merge([
                'rid'          => $rid,
                'op'           => $op,
                'car_id'       => $carId,
                'phase'        => $phase,
                'duration_ms'  => $durationMs,
            ], $extra));
        }
        return $result;
    }

    /**
     * DEPRECATED — kept for backward compatibility with any caller still
     * passing a stored path. The fast path is now
     * `optimizeUploadedFileInPlace($uploadedFile, $maxWidth)` BEFORE the
     * R2 upload, which avoids the GET + PUT round-trip entirely.
     */
    private function optimizeImage(string $storedPath, int $maxWidth): void
    {
        if (!function_exists('imagecreatefromjpeg')) return;

        $disk   = Storage::disk('public');
        $isS3   = config('filesystems.disks.public.driver') === 's3';

        if ($isS3) {
            if (!$disk->exists($storedPath)) return;
            $fullPath = tempnam(sys_get_temp_dir(), 'certicars_opt_');
            file_put_contents($fullPath, $disk->get($storedPath));
        } else {
            $fullPath = $disk->path($storedPath);
            if (!file_exists($fullPath)) return;
        }

        $info = @getimagesize($fullPath);
        if (!$info || $info[0] <= $maxWidth) {
            if ($isS3) @unlink($fullPath);
            return;
        }

        [$width, $height, $type] = $info;

        if ($type !== IMAGETYPE_JPEG) {
            if ($isS3) @unlink($fullPath);
            return;
        }

        try {
            $src = imagecreatefromjpeg($fullPath);
            if (!$src) {
                if ($isS3) @unlink($fullPath);
                return;
            }

            $newHeight = (int) round($height * $maxWidth / $width);
            $dst = imagescale($src, $maxWidth, $newHeight, IMG_BICUBIC);
            imagejpeg($dst, $fullPath, 85);
            imagedestroy($src);
            imagedestroy($dst);

            if ($isS3) {
                $disk->put($storedPath, file_get_contents($fullPath), 'public');
                @unlink($fullPath);
            }
        } catch (\Throwable) {
            if ($isS3) @unlink($fullPath);
        }
    }
}
