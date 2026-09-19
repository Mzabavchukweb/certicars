<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\ErrorLog;
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
            ErrorLog::record('car.store', 'Nowe auto — walidacja: ' . $this->firstErrors($ve), ['errors' => $ve->errors(), 'rid' => $reqId], 'warning');
            throw $ve;
        }
        $validated = $this->processEquipment($validated);
        unset($validated['image_alt'], $validated['active_tab']);

        try {
            // Phase 1: DB writes inside a transaction. If anything here throws,
            // NO partial car/relations remain in DB.
            $car = DB::transaction(function () use ($validated, $request) {
                $newCar = Car::create($validated);
                $this->syncRelations($newCar, $request);
                return $newCar;
            });
        } catch (\Throwable $e) {
            \Log::error('car.save.db_failed', [
                'rid' => $reqId, 'op' => 'store', 'user_id' => $userId,
                'exception' => get_class($e), 'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            ErrorLog::record('car.store', 'Nowe auto — zapis do bazy nie powiódł się: ' . $e->getMessage(), ['exception' => get_class($e), 'rid' => $reqId, 'trace' => mb_substr($e->getTraceAsString(), 0, 3000)]);
            $msg = 'Nie udało się zapisać samochodu (błąd bazy danych). Szczegóły są w Rejestrze błędów.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'detail' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', $msg);
        }

        // Phase 2: Image uploads — outside transaction (filesystem ops can't roll back).
        $imageFailures = [];
        try {
            $imageFailures = $this->handleImages($car, $request);
            $imageFailures = array_merge($imageFailures, $this->attachTempUploads($car, $request));
        } catch (\Throwable $e) {
            \Log::error('car.save.image_phase_failed', [
                'rid' => $reqId, 'op' => 'store', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
            ]);
            ErrorLog::record('car.store', 'Auto zapisane, ale pliki nie: ' . $e->getMessage(), ['exception' => get_class($e), 'rid' => $reqId], 'error', $car->id);
            $imageFailures[] = '(błąd przesyłania)';
        }

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        \Log::info('car.save.success', [
            'rid' => $reqId, 'op' => 'store', 'user_id' => $userId, 'car_id' => $car->id,
            'image_failures' => count($imageFailures),
        ]);

        if (!empty($imageFailures)) {
            ErrorLog::record('car.store', 'Auto zapisane, nie udało się dołączyć plików: ' . implode(', ', $imageFailures), [], 'warning', $car->id);
        }
        $redirect = redirect($this->editUrlWithTab($car, $request));
        if ($request->expectsJson()) {
            session()->flash('success', 'Samochód został dodany.');
            if (!empty($imageFailures)) session()->flash('warning', $this->formatImageFailureMessage($imageFailures));
            return response()->json(['success' => true, 'redirect' => $redirect->getTargetUrl(), 'car_id' => $car->id]);
        }
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
            ErrorLog::record('car.update', 'Edycja auta — walidacja: ' . $this->firstErrors($ve), ['errors' => $ve->errors(), 'rid' => $reqId], 'warning', $car->id);
            throw $ve;
        }
        $validated = $this->processEquipment($validated);
        unset($validated['image_alt'], $validated['active_tab']);

        try {
            // DB writes wrapped — if relations sync fails, the car update + relations roll back.
            DB::transaction(function () use ($car, $validated, $request) {
                $car->update($validated);
                $this->syncRelations($car, $request);
            });
        } catch (\Throwable $e) {
            \Log::error('car.save.db_failed', [
                'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            ErrorLog::record('car.update', 'Edycja auta — zapis do bazy nie powiódł się: ' . $e->getMessage(), ['exception' => get_class($e), 'rid' => $reqId, 'trace' => mb_substr($e->getTraceAsString(), 0, 3000)], 'error', $car->id);
            $msg = 'Nie udało się zaktualizować samochodu (błąd bazy danych). Twoje zmiany nie zostały zapisane — szczegóły są w Rejestrze błędów.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'detail' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', $msg);
        }

        $imageFailures = [];
        try {
            $imageFailures = $this->handleImages($car, $request);
        } catch (\Throwable $e) {
            \Log::error('car.save.image_phase_failed', [
                'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
                'exception' => get_class($e), 'message' => $e->getMessage(),
            ]);
            ErrorLog::record('car.update', 'Zmiany zapisane, ale pliki nie: ' . $e->getMessage(), ['exception' => get_class($e), 'rid' => $reqId], 'error', $car->id);
            $imageFailures[] = '(błąd przesyłania)';
        }

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        \Log::info('car.save.success', [
            'rid' => $reqId, 'op' => 'update', 'user_id' => $userId, 'car_id' => $car->id,
            'image_failures' => count($imageFailures),
        ]);

        if (!empty($imageFailures)) {
            ErrorLog::record('car.update', 'Zmiany zapisane, nie udało się dołączyć plików: ' . implode(', ', $imageFailures), [], 'warning', $car->id);
        }
        $redirect = redirect($this->editUrlWithTab($car, $request));
        if ($request->expectsJson()) {
            session()->flash('success', 'Samochód został zaktualizowany.');
            if (!empty($imageFailures)) session()->flash('warning', $this->formatImageFailureMessage($imageFailures));
            return response()->json(['success' => true, 'redirect' => $redirect->getTargetUrl(), 'car_id' => $car->id]);
        }
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
    private function firstErrors(\Illuminate\Validation\ValidationException $ve): string
    {
        return implode(' | ', array_map(fn($m) => $m[0] ?? '', array_slice($ve->errors(), 0, 6)));
    }

    private function safeFileCounts(Request $request): array
    {
        $out = [];
        foreach (['gallery_images', 'damage_images', 'pano360_image', 'pano360ext_image', 'interior_video_file', 'exterior_video_file'] as $field) {
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
        $path = $request->file('image')->store('cars/' . $car->id . '/' . $subdir, 'public');
        $this->optimizeImage($path, $type === 'damage' ? 1280 : 1920);

        $img = $car->images()->create([
            'path'       => $path,
            'type'       => $type,
            'is_primary' => $type === 'gallery' && !$car->images()->where('is_primary', true)->exists(),
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

    /** Pola filmow 360 / panoram i ich limity (MB) przy wysylce w czesciach. */
    private const MEDIA_LIMITS_MB = [
        'interior_video_file' => 500,
        'exterior_video_file' => 500,
        'pano360_image'       => 25,
        'pano360ext_image'    => 25,
    ];

    /** Sciezka z katalogu tymczasowego zalogowanego uzytkownika (i tylko taka). */
    private function isOwnTempPath($p): bool
    {
        return is_string($p)
            && preg_match('~^' . preg_quote($this->tempUploadDir(), '~') . '/[A-Za-z0-9]+\.[A-Za-z0-9]{2,5}$~', $p)
            && Storage::disk('public')->exists($p);
    }

    /**
     * Przenosi film/panorame z katalogu tymczasowego do auta: podmienia stary
     * plik, a dla filmu ustawia status klatek i kolejkuje ich wycinanie.
     */
    private function attachPendingMedia(Car $car, string $field, string $path): bool
    {
        if (!isset(self::MEDIA_LIMITS_MB[$field]) || !$this->isOwnTempPath($path)) return false;
        $disk = Storage::disk('public');

        if ($field === 'pano360_image' || $field === 'pano360ext_image') {
            $type = $field === 'pano360_image' ? 'pano360' : 'pano360ext';
            $dest = 'cars/' . $car->id . '/' . $type . '/' . basename($path);
            if (!$disk->move($path, $dest)) return false;
            $car->images()->where('type', $type)->get()->each(function ($old) use ($disk, $dest) {
                if ($old->path !== $dest && !str_starts_with($old->path, 'http')) $disk->delete($old->path);
                $old->delete();
            });
            $car->images()->create(['path' => $dest, 'type' => $type, 'sort_order' => 0]);
            return true;
        }

        $side = $field === 'interior_video_file' ? 'interior' : 'exterior';
        $dest = 'cars/' . $car->id . '/' . $side . '_video/' . basename($path);
        if (!$disk->move($path, $dest)) return false;

        $oldVideo  = $car->{$side . '_video_path'};
        $oldFrames = $car->{$side . '_frames_dir'};
        if ($oldVideo && $oldVideo !== $dest && !str_starts_with($oldVideo, 'http')) $disk->delete($oldVideo);
        if ($oldFrames) {
            try { foreach ($disk->files($oldFrames) as $f) $disk->delete($f); } catch (\Throwable $e) {}
        }

        $framesDir = 'cars/' . $car->id . '/' . $side . '_frames';
        $car->forceFill([
            $side . '_video_path'    => $dest,
            $side . '_frames_status' => 'pending',
            $side . '_frames_count'  => null,
            $side . '_frames_dir'    => $framesDir,
            $side . '_frames_error'  => null,
            $side . '_frames_meta'   => null,
        ])->save();

        $job = $side === 'interior' ? \App\Jobs\ExtractInteriorFramesJob::class : \App\Jobs\ExtractExteriorFramesJob::class;
        $job::dispatch($car->id, $dest, $framesDir);
        return true;
    }

    /**
     * Jedna czesc (do 5 MB) duzego pliku. Czesci doklejane sa do pliku .part na
     * dysku lokalnym. Klient podaje offset — ponowienie tej samej czesci po
     * zerwanym polaczeniu nie dubluje danych, a przy rozjezdzie serwer zwraca
     * 409 z aktualnym rozmiarem, od ktorego klient wznawia.
     * Po ostatniej czesci plik jest sprawdzany i trafia do tmp-uploads/{user}.
     */
    public function uploadChunk(Request $request)
    {
        $data = $request->validate([
            'upload_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{8,64}$/'],
            'kind'      => ['required', 'string'],
            'offset'    => ['required', 'integer', 'min:0'],
            'size'      => ['required', 'integer', 'min:1'],
            'name'      => ['required', 'string', 'max:255'],
            'chunk'     => ['required', 'file', 'max:6144'],
        ], [
            'chunk.uploaded' => 'Część pliku nie dotarła — ponawiam.',
        ]);

        // walidacja 'integer' nie rzutuje — porownania nizej musza byc na liczbach
        $data['offset'] = (int) $data['offset'];
        $data['size']   = (int) $data['size'];

        $kind = $data['kind'];
        if (!isset(self::MEDIA_LIMITS_MB[$kind])) {
            return response()->json(['success' => false, 'message' => 'Nieznany rodzaj pliku.'], 422);
        }
        $limitMb = self::MEDIA_LIMITS_MB[$kind];
        if ($data['size'] > $limitMb * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'Plik jest za duży. Maksymalny rozmiar to ' . $limitMb . ' MB.'], 422);
        }

        $dir = storage_path('app/chunks/' . (int) auth()->id());
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        // porzucone czesci i znaczniki starsze niz dobe
        foreach (array_merge(glob($dir . '/*.part') ?: [], glob($dir . '/*.done') ?: []) as $old) {
            if (@filemtime($old) < time() - 86400) @unlink($old);
        }

        // Ostatnia czesc juz przyjeta, ale odpowiedz zginela w sieci — oddaj ten sam wynik.
        $doneFile = $dir . '/' . $data['upload_id'] . '.done';
        if (is_file($doneFile)) {
            $donePath = trim((string) @file_get_contents($doneFile));
            if ($donePath !== '' && Storage::disk('public')->exists($donePath)) {
                return response()->json(['success' => true, 'done' => true, 'path' => $donePath]);
            }
        }

        $part    = $dir . '/' . $data['upload_id'] . '.part';
        $current = is_file($part) ? filesize($part) : 0;
        $chunk   = $request->file('chunk');
        $len     = $chunk->getSize();

        if ($data['offset'] === $current) {
            $in  = fopen($chunk->getRealPath(), 'rb');
            $out = fopen($part, 'ab');
            if (!$in || !$out || stream_copy_to_stream($in, $out) !== $len) {
                if ($in) fclose($in);
                if ($out) fclose($out);
                return response()->json(['success' => false, 'message' => 'Nie udało się zapisać części pliku.'], 500);
            }
            fclose($in);
            fclose($out);
            clearstatcache(true, $part);
            $current = filesize($part);
        } elseif ($data['offset'] + $len !== $current) {
            // rozjazd — klient wznowi od rozmiaru, ktory ma serwer
            return response()->json(['success' => false, 'resume_from' => $current, 'message' => 'Wznawianie od ' . $current . ' B.'], 409);
        }
        // (else: ta czesc juz byla zapisana — ponowienie po zerwaniu, nic nie dopisujemy)

        if ($current < $data['size']) {
            return response()->json(['success' => true, 'received' => $current]);
        }
        if ($current > $data['size']) {
            @unlink($part);
            return response()->json(['success' => false, 'message' => 'Plik dotarł uszkodzony — spróbuj ponownie.'], 422);
        }

        // Ostatnia czesc: sprawdz typ po zawartosci i przenies do tmp-uploads
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($part) ?: '';
        $isVideo = str_ends_with($kind, '_video_file');
        $allowed = $isVideo
            ? ['video/mp4' => 'mp4', 'video/quicktime' => 'mov', 'video/webm' => 'webm', 'video/x-msvideo' => 'avi', 'video/x-matroska' => 'mkv', 'application/octet-stream' => null]
            : ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!array_key_exists($mime, $allowed)) {
            @unlink($part);
            return response()->json(['success' => false, 'message' => $isVideo
                ? 'Nieobsługiwany format filmu. Dozwolone: MP4, MOV, WebM, AVI, MKV.'
                : 'Nieobsługiwany format panoramy. Dozwolone: JPG, PNG, WebP.'], 422);
        }
        $ext = $allowed[$mime];
        if ($ext === null) {
            // niektore MOV/MP4 finfo widzi jako octet-stream — wtedy decyduje rozszerzenie
            $ext = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['mp4', 'mov', 'webm', 'avi', 'mkv', 'm4v'], true)) {
                @unlink($part);
                return response()->json(['success' => false, 'message' => 'Nieobsługiwany format filmu. Dozwolone: MP4, MOV, WebM, AVI, MKV.'], 422);
            }
        }

        $name = \Illuminate\Support\Str::random(40) . '.' . $ext;
        try {
            $stored = Storage::disk('public')->putFileAs($this->tempUploadDir(), new \Illuminate\Http\File($part), $name);
        } catch (\Throwable $e) {
            \Log::error('chunk.finalize_failed', ['err' => $e->getMessage()]);
            $stored = false;
        }
        @unlink($part);
        if (!$stored) {
            ErrorLog::record('upload.chunk', 'Nie udało się zapisać pliku po złożeniu części: ' . $data['name'], ['kind' => $kind, 'size' => $data['size']]);
            return response()->json(['success' => false, 'message' => 'Nie udało się zapisać pliku na serwerze.'], 500);
        }
        @file_put_contents($doneFile, $stored);
        return response()->json(['success' => true, 'done' => true, 'path' => $stored]);
    }

    /** Istniejace auto: przypnij film/panorame wgrana w czesciach. */
    public function attachMedia(Request $request, Car $car)
    {
        $field = (string) $request->input('field');
        $path  = (string) $request->input('path');
        if (!$this->attachPendingMedia($car, $field, $path)) {
            return response()->json(['success' => false, 'message' => 'Nie udało się dołączyć pliku do ogłoszenia.'], 422);
        }
        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');
        return response()->json(['success' => true]);
    }

    /** Rodzaje plikow wgrywanych od razu przy nowym aucie + ich reguly walidacji. */
    private function tempUploadRules(): array
    {
        $image = 'image|mimes:jpg,jpeg,png,webp,avif|max:20480';
        $video = 'file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:204800';
        $pano  = 'image|mimes:jpg,jpeg,png,webp|max:25600';
        return [
            'gallery'             => $image,
            'damage'              => $image,
            'damage_marker'       => $image,
            'interior_video_file' => $video,
            'exterior_video_file' => $video,
            'pano360_image'       => $pano,
            'pano360ext_image'    => $pano,
        ];
    }

    private function tempUploadDir(): string
    {
        return 'tmp-uploads/' . (int) auth()->id();
    }

    /**
     * Nowe auto nie ma jeszcze id, wiec pliki trafiaja od razu do katalogu
     * tymczasowego uzytkownika. Przy zapisie auta attachTempUploads() je
     * przenosi. Dzieki temu wgrywanie trwa w trakcie wypelniania formularza,
     * a nie dopiero po kliknieciu "Opublikuj".
     */
    public function uploadTemp(Request $request)
    {
        $rules = $this->tempUploadRules();
        $kind  = (string) $request->input('kind');
        if (!isset($rules[$kind])) {
            return response()->json(['success' => false, 'message' => 'Nieznany rodzaj pliku.'], 422);
        }
        $isVideo = str_ends_with($kind, '_video_file');
        $limit   = $isVideo ? '200' : (str_starts_with($kind, 'pano') ? '25' : '20');
        $request->validate(['file' => 'required|' . $rules[$kind]], [
            'file.required'  => 'Nie dotarł plik — przesyłanie zostało przerwane.',
            'file.uploaded'  => 'Plik jest za duży lub przesyłanie zostało przerwane. Maksymalny rozmiar to ' . $limit . ' MB.',
            'file.max'       => 'Plik jest za duży. Maksymalny rozmiar to ' . $limit . ' MB.',
            'file.mimetypes' => 'Nieobsługiwany format filmu. Dozwolone: MP4, WebM, MOV, AVI, MKV.',
            'file.mimes'     => 'Nieobsługiwany format zdjęcia. Dozwolone: JPG, PNG, WebP, AVIF.',
            'file.image'     => 'Plik musi być zdjęciem.',
            'file.file'      => 'Plik jest uszkodzony.',
        ]);

        $disk = Storage::disk('public');
        $dir  = $this->tempUploadDir();

        // Sprzatanie porzuconych plikow (formularz zamkniety bez zapisu) — starsze niz 2 dni.
        try {
            foreach ($disk->files($dir) as $old) {
                if ($disk->lastModified($old) < now()->subDays(2)->getTimestamp()) {
                    $disk->delete($old);
                }
            }
        } catch (\Throwable $e) {
            // best-effort
        }

        $path = $this->safeStore($request->file('file'), $dir);
        if ($path === null) {
            ErrorLog::record('upload.temp', 'Nie udało się zapisać pliku na serwerze: ' . $request->file('file')->getClientOriginalName(), ['kind' => $kind]);
            return response()->json(['success' => false, 'message' => 'Nie udało się zapisać pliku na serwerze.'], 500);
        }
        if ($kind === 'gallery' || $kind === 'damage' || $kind === 'damage_marker') {
            $this->optimizeImage($path, $kind === 'gallery' ? 1920 : 1280);
        }

        return response()->json(['success' => true, 'path' => $path, 'url' => $disk->url($path)]);
    }

    /**
     * Przypina do nowego auta pliki wgrane wczesniej przez uploadTemp().
     * Przyjmuje TYLKO sciezki z katalogu tymczasowego zalogowanego uzytkownika.
     */
    private function attachTempUploads(Car $car, Request $request): array
    {
        $failures = [];
        $disk = Storage::disk('public');
        $dir  = $this->tempUploadDir();
        $ok = function ($p) use ($disk, $dir) {
            return is_string($p)
                && preg_match('~^' . preg_quote($dir, '~') . '/[A-Za-z0-9]+\.[A-Za-z0-9]{2,5}$~', $p)
                && $disk->exists($p);
        };
        $moveTo = function (string $p, string $sub) use ($disk, $car): ?string {
            $dest = 'cars/' . $car->id . '/' . $sub . '/' . basename($p);
            try {
                return $disk->move($p, $dest) ? $dest : null;
            } catch (\Throwable $e) {
                \Log::error('temp.attach.move_failed', ['car_id' => $car->id, 'path' => $p, 'err' => $e->getMessage()]);
                return null;
            }
        };

        foreach (['gallery' => 'pending_gallery', 'damage' => 'pending_damage'] as $type => $field) {
            foreach ((array) $request->input($field, []) as $p) {
                $dest = $ok($p) ? $moveTo($p, $type) : null;
                if ($dest === null) { $failures[] = basename((string) $p); continue; }
                $car->images()->create([
                    'path'       => $dest,
                    'type'       => $type,
                    'is_primary' => $type === 'gallery' && !$car->images()->where('is_primary', true)->exists(),
                    'sort_order' => ($car->images()->max('sort_order') ?? 0) + 1,
                ]);
            }
        }

        foreach (array_keys(self::MEDIA_LIMITS_MB) as $field) {
            $p = $request->input('pending_' . $field);
            if (!$p) continue;
            if (!$this->attachPendingMedia($car, $field, (string) $p)) {
                $failures[] = basename((string) $p);
            }
        }

        return $failures;
    }

    /**
     * Pojedynczy film 360 albo panorama — osobnym zadaniem, zeby formularz auta
     * nie wysylal wszystkiego naraz (nginx: 413 Request Entity Too Large).
     * Obsluga plikow jest ta sama co przy zapisie formularza (handleImages).
     */
    public function uploadMedia(Request $request, Car $car)
    {
        $video = 'file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:204800';
        $pano  = 'image|mimes:jpg,jpeg,png,webp|max:25600';
        $rules = [
            'interior_video_file' => $video,
            'exterior_video_file' => $video,
            'pano360_image'       => $pano,
            'pano360ext_image'    => $pano,
        ];
        $labels = [
            'interior_video_file' => 'Film 360° wnętrza',
            'exterior_video_file' => 'Film 360° na zewnątrz',
            'pano360_image'       => 'Panorama 360° wnętrza',
            'pano360ext_image'    => 'Panorama 360° na zewnątrz',
        ];

        $field = (string) $request->input('field');
        if (!isset($rules[$field])) {
            return response()->json(['success' => false, 'message' => 'Nieznane pole pliku.'], 422);
        }

        $isVideo = str_ends_with($field, '_video_file');
        $request->validate([$field => 'required|' . $rules[$field]], [
            $field . '.required'  => $labels[$field] . ': nie dotarł plik — przesyłanie zostało przerwane.',
            $field . '.uploaded'  => $labels[$field] . ' jest za duży lub przesyłanie zostało przerwane. Maksymalny rozmiar to ' . ($isVideo ? '200' : '25') . ' MB.',
            $field . '.max'       => $labels[$field] . ' jest za duży. Maksymalny rozmiar to ' . ($isVideo ? '200' : '25') . ' MB.',
            $field . '.mimetypes' => $labels[$field] . ': nieobsługiwany format. Dozwolone: MP4, WebM, MOV, AVI, MKV.',
            $field . '.mimes'     => $labels[$field] . ': nieobsługiwany format. Dozwolone: JPG, PNG, WebP.',
            $field . '.image'     => $labels[$field] . ' musi być zdjęciem.',
            $field . '.file'      => $labels[$field] . ' musi być prawidłowym plikiem.',
        ]);

        // Request zawiera tylko ten jeden plik, wiec handleImages obsluzy tylko jego.
        $failures = $this->handleImages($car, $request);

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        if ($failures) {
            return response()->json(['success' => false, 'message' => $labels[$field] . ': nie udało się zapisać pliku na serwerze.'], 500);
        }
        return response()->json(['success' => true]);
    }

    /** Zapis kolejności zdjęć galerii zaraz po przeciągnięciu (AJAX). */
    public function reorderImages(Request $request, Car $car)
    {
        $data = $request->validate([
            'order'   => 'required|array|max:200',
            'order.*' => 'integer',
        ]);

        DB::transaction(function () use ($car, $data) {
            foreach (array_values($data['order']) as $position => $imageId) {
                $car->images()->where('id', (int) $imageId)->where('type', 'gallery')
                    ->update(['sort_order' => $position]);
            }
        });

        // Rownolegle wgrywanie moglo oznaczyc kilka zdjec jako glowne — zostaw pierwsze.
        $primaries = $car->images()->where('is_primary', true)->orderBy('sort_order')->pluck('id');
        if ($primaries->count() > 1) {
            $car->images()->whereIn('id', $primaries->slice(1)->all())->update(['is_primary' => false]);
        }

        Cache::forget('catalog.filters');
        Cache::forget('sitemap.xml');

        return response()->json(['success' => true]);
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

        // Highlighted equipment — array of up to 8 option keys from
        // EquipmentCatalog::OPTIONS. Empty slots are submitted as empty strings
        // by the <select> and dropped here. Unknown keys are also filtered.
        if (array_key_exists('highlighted_equipment', $validated)) {
            $valid = array_keys(\App\Helpers\EquipmentCatalog::OPTIONS);
            $cleaned = [];
            foreach ((array) ($validated['highlighted_equipment'] ?? []) as $key) {
                $key = is_string($key) ? trim($key) : '';
                if ($key !== '' && in_array($key, $valid, true)) {
                    $cleaned[] = $key;
                }
            }
            $validated['highlighted_equipment'] = $cleaned ?: null;
        }

        // technical_conditions storage shape:
        //   { key => { status: 'ok'|'attention'|'bad', note: string } }
        // — preserved per item so the wizard's status pills + note input
        // round-trip cleanly. Backward-compat with the legacy flat-string
        // shape (PR #2): legacy strings are accepted as-is on read via
        // CarLabels::techStatus(), and the form upgrades them to the
        // nested shape on the next save. Unknown / empty status falls
        // back to 'ok'.
        if (!empty($validated['technical_conditions']) && is_array($validated['technical_conditions'])) {
            $allowedStatuses = ['ok', 'attention', 'bad'];
            $normalized = [];
            foreach ($validated['technical_conditions'] as $key => $value) {
                if (is_array($value)) {
                    $rawStatus = isset($value['status']) ? strtolower(trim((string) $value['status'])) : '';
                    $status = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'ok';
                    $note   = isset($value['note']) ? trim((string) $value['note']) : '';
                    if (mb_strlen($note) > 500) $note = mb_substr($note, 0, 500);
                    $normalized[$key] = ['status' => $status, 'note' => $note];
                } elseif (is_string($value) && trim($value) !== '') {
                    // Legacy free-text from old wizards — keep the string for now;
                    // CarLabels::techStatus() will derive a colour from it on render.
                    $normalized[$key] = $value;
                } elseif (is_scalar($value)) {
                    $normalized[$key] = (string) $value;
                }
                // null / empty strings are dropped (no row written) so old data without
                // status doesn't clutter the JSON.
            }
            $validated['technical_conditions'] = $normalized;
        }

        return $validated;
    }

    /**
     * Liczby wpisywane po polsku: "73.000", "73 000 km", "89 900 zł", "1.995 cm³",
     * "89.900,50". Kropka/spacja jako separator tysięcy nie może wywalać zapisu
     * ("Pole mileage musi być liczbą całkowitą") — zamieniamy na czystą liczbę.
     */
    public static function normalizeNumber($value, bool $integer)
    {
        if ($value === null || is_int($value) || is_float($value)) return $value;
        $v = trim(str_replace(["\u{00A0}", "\u{202F}"], ' ', (string) $value));
        if ($v === '') return $value;
        $v = preg_replace('/[^\d.,\s-]/u', '', $v);          // km, zł, KM, cm³…
        $v = preg_replace('/\s+/', '', $v);
        if ($v === '' || $v === '-') return $value;
        if (preg_match('/^-?\d{1,3}([.,]\d{3})+$/', $v)) {      // 73.000 / 1,995 / 1.234.567
            $v = str_replace(['.', ','], '', $v);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+,\d+$/', $v)) { // 89.900,50
            $v = str_replace(['.', ','], ['', '.'], $v);
        } elseif (preg_match('/^-?\d{1,3}(,\d{3})+\.\d+$/', $v)) { // 89,900.50
            $v = str_replace(',', '', $v);
        } else {
            $v = str_replace(',', '.', $v);                      // 5,6 -> 5.6
        }
        if (!is_numeric($v)) return $value;                      // niech walidacja powie, co nie tak
        return $integer ? (string) (int) round((float) $v) : $v;
    }

    private function validateCar(Request $request): array
    {
        $normalized = [];
        foreach (['mileage', 'seats', 'weight', 'production_year', 'power_hp', 'power_kw', 'engine_capacity', 'last_service_mileage', 'doors'] as $f) {
            if ($request->has($f)) $normalized[$f] = self::normalizeNumber($request->input($f), true);
        }
        if ($request->has('price')) $normalized['price'] = self::normalizeNumber($request->input('price'), false);
        $request->merge($normalized);

        $validated = $request->validate([
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
            'production_year' => 'nullable|integer|min:1900|max:2100',
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
            'engine_version'      => 'nullable|string|max:120',
            'equipment_version'   => 'nullable|string|max:120',
            'drivetrain'          => 'nullable|string|max:80',
            'location' => 'nullable|string|max:255',
            'location_distance' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:255',
            'is_imported' => 'nullable|boolean',
            'country_registration' => 'nullable|string|max:100',
            'imported_from' => 'nullable|string|max:100',
            'vehicle_history' => 'nullable|string|max:100',
            'last_service' => 'nullable|string|max:100',
            'last_service_mileage' => 'nullable|string|max:100',
            'last_service_scope' => 'nullable|string|max:255',
            'service_confirmation_type' => 'nullable|string|max:120',
            'odometer_status' => 'nullable|string|max:120',
            'de_tech_valid_until' => 'nullable|string|max:50',
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
            // Interior 360° pan-around video — ffmpeg slices it into a JPEG
            // frame sequence (see ExtractInteriorFramesJob) for the catalog
            // page's drag-scrubber. 200 MB cap so typical phone clips upload
            // in one go.
            'interior_video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:204800',
            'remove_interior_video' => 'nullable|boolean',
            // Exterior 360° walk-around video — same pipeline as interior, see
            // ExtractExteriorFramesJob. 200 MB cap matches interior.
            'exterior_video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska|max:204800',
            'remove_exterior_video' => 'nullable|boolean',
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
            'technical_conditions'          => 'nullable|array',
            'technical_conditions.*'        => 'nullable',                       // accept legacy string OR new nested shape
            'technical_conditions.*.status' => 'nullable|in:ok,attention,bad',   // explicit enum when nested
            'technical_conditions.*.note'   => 'nullable|string|max:500',
            'equipment' => 'nullable|array',
            'highlighted_equipment'   => 'nullable|array|max:6',
            'highlighted_equipment.*' => 'nullable|string|max:64',
        ], [
            'brand_id.required' => 'Wybierz markę pojazdu.',
            'brand_id.exists'   => 'Wybrana marka nie istnieje w systemie.',
            'model.required'    => 'Podaj model pojazdu.',
            'model.max'         => 'Model pojazdu może mieć maksymalnie 255 znaków.',
            // Engine video — clear Polish error instead of empty-form reset.
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
            'interior_video_file.file'       => 'Film wnętrza 360° musi być prawidłowym plikiem wideo.',
            'interior_video_file.mimetypes'  => 'Nieobsługiwany format wideo wnętrza. Dozwolone: MP4, WebM, MOV (QuickTime), AVI, MKV.',
            'interior_video_file.max'        => 'Film wnętrza 360° jest za duży. Maksymalny rozmiar to 200 MB.',
            'interior_video_file.uploaded'   => 'Film wnętrza 360° jest za duży lub przesyłanie zostało przerwane. Maksymalny rozmiar to 200 MB.',
            'exterior_video_file.file'       => 'Film zewnętrza 360° musi być prawidłowym plikiem wideo.',
            'exterior_video_file.mimetypes'  => 'Nieobsługiwany format wideo zewnętrza. Dozwolone: MP4, WebM, MOV (QuickTime), AVI, MKV.',
            'exterior_video_file.max'        => 'Film zewnętrza 360° jest za duży. Maksymalny rozmiar to 200 MB.',
            'exterior_video_file.uploaded'   => 'Film zewnętrza 360° jest za duży lub przesyłanie zostało przerwane. Maksymalny rozmiar to 200 MB.',
        ], [
            'mileage'              => 'Przebieg',
            'price'                => 'Cena',
            'seats'                => 'Liczba miejsc',
            'production_year'      => 'Rok produkcji',
            'power_hp'             => 'Moc (KM)',
            'power_kw'             => 'Moc (kW)',
            'engine_capacity'      => 'Pojemność skokowa',
            'last_service_mileage' => 'Przebieg przy serwisie',
            'brand_id'             => 'Marka',
            'model'                => 'Model',
            'vin'                  => 'VIN',
            'first_registration'   => 'Pierwsza rejestracja',
        ]);

        // Kolumny boolean sa NOT NULL z domyslnym false — pusty select ("— wybierz —")
        // przychodzi jako null i wywalal INSERT/UPDATE calego auta.
        foreach (['is_imported', 'available_now', 'home_delivery', 'has_certicheck', 'has_gethelp', 'noindex'] as $boolField) {
            if (array_key_exists($boolField, $validated) && $validated[$boolField] === null) {
                $validated[$boolField] = false;
            }
        }

        return $validated;
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

                // Zdjecia oznaczen wgrane od razu po wybraniu (tmp-uploads) — przenies do auta.
                if (!empty($damage['pending_images']) && is_array($damage['pending_images'])) {
                    Storage::disk('public')->makeDirectory('cars/' . $car->id . '/damages');
                    $sortOrder = $dmgRecord->photos()->max('sort_order') ?? 0;
                    foreach ($damage['pending_images'] as $tmp) {
                        if (!$this->isOwnTempPath($tmp)) continue;
                        $dest = 'cars/' . $car->id . '/damages/' . basename($tmp);
                        try {
                            if (!Storage::disk('public')->move($tmp, $dest)) continue;
                        } catch (\Throwable $e) {
                            ErrorLog::record('car.damages', 'Nie udało się przenieść zdjęcia oznaczenia: ' . $e->getMessage(), ['tmp' => $tmp], 'error', $car->id);
                            continue;
                        }
                        \App\Models\CarImage::create([
                            'car_id'     => $car->id,
                            'damage_id'  => $dmgRecord->id,
                            'path'       => $dest,
                            'type'       => 'damage',
                            'sort_order' => ++$sortOrder,
                        ]);
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

        // Ensure upload directories exist
        $basePath = 'cars/' . $car->id;
        foreach (['gallery', 'damage', 'pano360', 'pano360ext'] as $sub) {
            Storage::disk('public')->makeDirectory($basePath . '/' . $sub);
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $this->safeStore($file, 'cars/' . $car->id . '/gallery');
                if ($path === null) {
                    $failures[] = $file->getClientOriginalName();
                    continue;
                }
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
                $path = $this->safeStore($file, 'cars/' . $car->id . '/damage');
                if ($path === null) {
                    $failures[] = $file->getClientOriginalName();
                    continue;
                }
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
            $panoFile = $request->file('pano360_image');
            $path = $this->safeStore($panoFile, 'cars/' . $car->id . '/pano360');
            if ($path === null) {
                $failures[] = $panoFile->getClientOriginalName();
            } else {
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
            }
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
            $path = $this->safeStore($panoExtFile, 'cars/' . $car->id . '/pano360ext');
            if ($path === null) {
                $failures[] = $panoExtFile->getClientOriginalName();
            } else {
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
            }
        }

        // ===== 360° interior — video → frame sequence (Copart-style scrubber) =====
        $this->handleInteriorVideo($car, $request, $failures);

        // ===== 360° exterior — same pipeline, walk-around video =====
        $this->handleExteriorVideo($car, $request, $failures);

        return $failures;
    }

    /**
     * Persist a newly uploaded interior pan-around video and queue the frame
     * extraction job. On removal, deletes both the video and every previously
     * extracted frame so the catalog page falls back to the equirectangular
     * pano (if present) or hides the interior card altogether.
     *
     * Failures append to $failures by reference so the operator sees a single
     * combined toast — never throws.
     */
    private function handleInteriorVideo(Car $car, Request $request, array &$failures): void
    {
        $disk = Storage::disk('public');

        if ($request->boolean('remove_interior_video')) {
            if ($car->interior_video_path && !str_starts_with($car->interior_video_path, 'http')) {
                $disk->delete($car->interior_video_path);
            }
            if ($car->interior_frames_dir) {
                try {
                    foreach ($disk->files($car->interior_frames_dir) as $existing) {
                        $disk->delete($existing);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('interior.frames.cleanup_failed', ['car_id' => $car->id, 'err' => $e->getMessage()]);
                }
            }
            $car->forceFill([
                'interior_video_path'    => null,
                'interior_frames_status' => null,
                'interior_frames_count'  => null,
                'interior_frames_dir'    => null,
                'interior_frames_error'  => null,
            'interior_frames_meta'   => null,
            ])->save();
        }

        if (!$request->hasFile('interior_video_file')) return;

        $videoFile = $request->file('interior_video_file');
        $newPath = $this->safeStore($videoFile, 'cars/' . $car->id . '/interior_video');
        if ($newPath === null) {
            $failures[] = $videoFile->getClientOriginalName();
            return;
        }

        // Delete the previous video (and any previously extracted frames) only
        // AFTER the new upload succeeded — keeps catalog rendering intact if
        // R2/S3 transiently fails on the new upload.
        if ($car->interior_video_path && $car->interior_video_path !== $newPath && !str_starts_with($car->interior_video_path, 'http')) {
            $disk->delete($car->interior_video_path);
        }
        if ($car->interior_frames_dir) {
            try {
                foreach ($disk->files($car->interior_frames_dir) as $existing) {
                    $disk->delete($existing);
                }
            } catch (\Throwable $e) {
                // Best-effort; the extractor will overwrite anyway.
            }
        }

        $framesDir = 'cars/' . $car->id . '/interior_frames';
        $car->forceFill([
            'interior_video_path'    => $newPath,
            'interior_frames_status' => 'pending',
            'interior_frames_count'  => null,
            'interior_frames_dir'    => $framesDir,
            'interior_frames_error'  => null,
            'interior_frames_meta'   => null,
        ])->save();

        \App\Jobs\ExtractInteriorFramesJob::dispatch($car->id, $newPath, $framesDir);
    }

    /**
     * Persist a newly uploaded exterior walk-around video and queue its frame
     * extraction. Mirrors handleInteriorVideo() — kept as a separate method so
     * the two video paths can diverge later (e.g. different mime caps, extra
     * vehicle-mask post-processing on exterior) without untangling shared
     * state. Never throws; failures append to $failures by reference.
     */
    private function handleExteriorVideo(Car $car, Request $request, array &$failures): void
    {
        $disk = Storage::disk('public');

        if ($request->boolean('remove_exterior_video')) {
            if ($car->exterior_video_path && !str_starts_with($car->exterior_video_path, 'http')) {
                $disk->delete($car->exterior_video_path);
            }
            if ($car->exterior_frames_dir) {
                try {
                    foreach ($disk->files($car->exterior_frames_dir) as $existing) {
                        $disk->delete($existing);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('exterior.frames.cleanup_failed', ['car_id' => $car->id, 'err' => $e->getMessage()]);
                }
            }
            $car->forceFill([
                'exterior_video_path'    => null,
                'exterior_frames_status' => null,
                'exterior_frames_count'  => null,
                'exterior_frames_dir'    => null,
                'exterior_frames_error'  => null,
            'exterior_frames_meta'   => null,
            ])->save();
        }

        if (!$request->hasFile('exterior_video_file')) return;

        $videoFile = $request->file('exterior_video_file');
        $newPath = $this->safeStore($videoFile, 'cars/' . $car->id . '/exterior_video');
        if ($newPath === null) {
            $failures[] = $videoFile->getClientOriginalName();
            return;
        }

        if ($car->exterior_video_path && $car->exterior_video_path !== $newPath && !str_starts_with($car->exterior_video_path, 'http')) {
            $disk->delete($car->exterior_video_path);
        }
        if ($car->exterior_frames_dir) {
            try {
                foreach ($disk->files($car->exterior_frames_dir) as $existing) {
                    $disk->delete($existing);
                }
            } catch (\Throwable $e) {
                // Best-effort; the extractor will overwrite anyway.
            }
        }

        $framesDir = 'cars/' . $car->id . '/exterior_frames';
        $car->forceFill([
            'exterior_video_path'    => $newPath,
            'exterior_frames_status' => 'pending',
            'exterior_frames_count'  => null,
            'exterior_frames_dir'    => $framesDir,
            'exterior_frames_error'  => null,
            'exterior_frames_meta'   => null,
        ])->save();

        \App\Jobs\ExtractExteriorFramesJob::dispatch($car->id, $newPath, $framesDir);
    }

    /**
     * Resize and re-compress an image stored on the public disk using PHP GD.
     * Also bakes EXIF orientation into pixel data so re-encoded JPEGs don't
     * end up rotated 90/180° in the browser. Skips if GD is not available
     * or the file is missing.
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
        if (!$info) {
            if ($isS3) @unlink($fullPath);
            return;
        }

        [$width, $height, $type] = $info;

        // Only recompress JPEG — PNG/WebP would be silently reformatted to JPEG
        // while keeping the original extension, causing browser decode errors.
        if ($type !== IMAGETYPE_JPEG) {
            if ($isS3) @unlink($fullPath);
            return;
        }

        // Read EXIF orientation. Re-encoding without baking this in is what
        // produced the "upside-down" thumbnails: browsers respect EXIF on the
        // original JPEG, but the tag is dropped when GD re-encodes, leaving
        // the pixel data oriented per the camera sensor not the desired view.
        $orientation = 1;
        if (function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($fullPath);
                if (is_array($exif) && isset($exif['Orientation'])) {
                    $orientation = (int) $exif['Orientation'];
                }
            } catch (\Throwable) {
                $orientation = 1;
            }
        }

        $needsRotate = in_array($orientation, [3, 6, 8], true);
        $needsResize = $width > $maxWidth;
        if (!$needsRotate && !$needsResize) {
            if ($isS3) @unlink($fullPath);
            return;
        }

        try {
            $src = imagecreatefromjpeg($fullPath);
            if (!$src) {
                if ($isS3) @unlink($fullPath);
                return;
            }

            // Bake the EXIF rotation into pixel data BEFORE the resize so the
            // resize math uses the post-rotation dimensions.
            if ($needsRotate) {
                $angle = match ($orientation) {
                    3 => 180,
                    6 => -90, // GD imagerotate is counter-clockwise
                    8 => 90,
                    default => 0,
                };
                if ($angle !== 0) {
                    $rotated = imagerotate($src, $angle, 0);
                    if ($rotated !== false) {
                        imagedestroy($src);
                        $src = $rotated;
                        if (in_array($orientation, [6, 8], true)) {
                            [$width, $height] = [$height, $width];
                        }
                    }
                }
            }

            if ($width > $maxWidth) {
                $newHeight = (int) round($height * $maxWidth / $width);
                $dst = imagescale($src, $maxWidth, $newHeight, IMG_BICUBIC);
                if ($dst !== false) {
                    imagedestroy($src);
                    $src = $dst;
                }
            }

            imagejpeg($src, $fullPath, 85);
            imagedestroy($src);

            if ($isS3) {
                $disk->put($storedPath, file_get_contents($fullPath), 'public');
                @unlink($fullPath);
            }
        } catch (\Throwable) {
            // Optimization failed — original file kept as-is.
            if ($isS3) @unlink($fullPath);
        }
    }
}
