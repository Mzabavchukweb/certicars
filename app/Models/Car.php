<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Car extends Model
{
    protected $fillable = [
        'brand_id', 'model', 'slug', 'category', 'identifier',
        'price', 'currency', 'price_type',
        'seller_name', 'seller_phone', 'seller_email', 'commission_note', 'reception_date',
        'color', 'color_code', 'doors', 'seats', 'weight', 'upholstery', 'vin', 'body_type',
        'first_registration', 'production_year', 'mileage', 'previous_owners', 'business_use', 'number_of_keys',
        'fuel_type', 'power_hp', 'power_kw', 'engine_capacity', 'engine_version', 'transmission', 'transmission_detail', 'equipment_version', 'drivetrain',
        'location', 'location_distance', 'source', 'is_imported', 'country_registration', 'taxation', 'imported_from', 'vehicle_history',
        'last_service', 'last_service_mileage', 'last_service_scope', 'next_inspection', 'de_tech_valid_until', 'service_documentation', 'service_confirmation_type', 'odometer_status',
        'fuel_consumption', 'fuel_procedure', 'co2_emission', 'emission_class',
        'service_book', 'coc_documents', 'vehicle_folder', 'hu_au_report',
        'service_book_status', 'registration_cert', 'owners_manual', 'aso_serviced', 'service_history',
        'paint_measurements', 'technical_conditions', 'equipment', 'highlighted_equipment', 'engine_video_url', 'engine_video_path',
        // Interior 360° (Copart-style frame scrubber) — see migration add_interior_360_video_to_cars_table.
        'interior_video_path', 'interior_frames_status', 'interior_frames_count', 'interior_frames_dir', 'interior_frames_error',
        // Exterior 360° walk-around (same scrubber pipeline) — see migration add_exterior_360_video_to_cars_table.
        'exterior_video_path', 'exterior_frames_status', 'exterior_frames_count', 'exterior_frames_dir', 'exterior_frames_error',
        'is_featured', 'is_sold', 'has_certicheck', 'available_now', 'home_delivery', 'has_gethelp', 'gethelp_package', 'status',
        'meta_title', 'meta_description', 'focus_keyword', 'noindex',
        // Cached-brochure state — see migration add_brochure_columns_to_cars_table.
        'brochure_path', 'brochure_status', 'brochure_generated_at', 'brochure_size', 'brochure_error',
    ];

    protected $casts = [
        'paint_measurements' => 'array',
        'technical_conditions' => 'array',
        'equipment' => 'array',
        'highlighted_equipment' => 'array',
        'is_featured' => 'boolean',
        'is_sold' => 'boolean',
        'has_certicheck' => 'boolean',
        'is_imported' => 'boolean',
        'available_now' => 'boolean',
        'home_delivery' => 'boolean',
        'has_gethelp' => 'boolean',
        'noindex' => 'boolean',
        'price' => 'decimal:2',
        'reception_date' => 'date',
        'brochure_generated_at' => 'datetime',
    ];

    /** True iff the public download endpoint is allowed to serve a PDF. */
    public function brochureIsReady(): bool
    {
        return $this->has_certicheck
            && $this->brochure_status === 'ready'
            && !empty($this->brochure_path);
    }

    protected static function booted(): void
    {
        static::creating(function (Car $car) {
            if (empty($car->slug)) {
                $base = Str::slug(($car->brand?->name ?? 'car') . ' ' . $car->model);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $car->slug = $slug;
            }
            if (empty($car->identifier)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $car->identifier = 'CC-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (Car $car) {
            // Keep is_sold and status consistent so neither UI path can desynchronize them.
            if ($car->isDirty('status') && $car->status === 'sold') {
                $car->is_sold = true;
            }
            if ($car->isDirty('is_sold') && $car->is_sold && $car->status !== 'sold') {
                $car->status = 'sold';
            }
            if ($car->isDirty('is_sold') && !$car->is_sold && $car->status === 'sold') {
                $car->status = 'active';
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(CarImage::class)->where('type', 'gallery')->orderBy('sort_order');
    }

    public function damageImages(): HasMany
    {
        return $this->hasMany(CarImage::class)->where('type', 'damage')->orderBy('sort_order');
    }

    public function pano360Image()
    {
        return $this->hasOne(CarImage::class)->where('type', 'pano360');
    }

    public function exteriorPano360Image()
    {
        return $this->hasOne(CarImage::class)->where('type', 'pano360ext');
    }

    /** True iff the catalog page should switch the interior 360° card to the frame-scrubber. */
    public function hasInteriorFrames(): bool
    {
        return $this->interior_frames_status === 'ready'
            && (int) $this->interior_frames_count > 0
            && !empty($this->interior_frames_dir);
    }

    /**
     * Build a sequenced list of public URLs for the extracted interior frames.
     * Used by the catalog page's drag-scrubber. Returns [] when frames are not
     * ready so the Blade view can fall back to the equirectangular interior
     * Pannellum embed.
     */
    public function interiorFrameUrls(): array
    {
        if (!$this->hasInteriorFrames()) {
            return [];
        }
        return $this->buildFrameUrls($this->interior_frames_dir, (int) $this->interior_frames_count);
    }

    /** True iff the catalog page should switch the exterior 360° card to the frame-scrubber. */
    public function hasExteriorFrames(): bool
    {
        return $this->exterior_frames_status === 'ready'
            && (int) $this->exterior_frames_count > 0
            && !empty($this->exterior_frames_dir);
    }

    /**
     * Sequenced public URLs for the extracted exterior walk-around frames.
     * Returns [] when frames are not ready so the Blade view can fall back to
     * the equirectangular exterior Pannellum embed (legacy uploads).
     */
    public function exteriorFrameUrls(): array
    {
        if (!$this->hasExteriorFrames()) {
            return [];
        }
        return $this->buildFrameUrls($this->exterior_frames_dir, (int) $this->exterior_frames_count);
    }

    private function buildFrameUrls(string $dir, int $count): array
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $urls = [];
        for ($i = 1; $i <= $count; $i++) {
            $urls[] = $disk->url($dir . '/' . sprintf('frame_%03d.jpg', $i));
        }
        return $urls;
    }

    public function damages(): HasMany
    {
        return $this->hasMany(CarDamage::class);
    }

    public function tireSets(): HasMany
    {
        return $this->hasMany(CarTireSet::class)->orderBy('set_number');
    }

    public function views(): HasMany
    {
        return $this->hasMany(CarView::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_sold', false)->where('status', 'active');
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images->where('is_primary', true)->first() ?? $this->images->first();
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) return 'Cena na zapytanie';
        $symbol = $this->currency === 'PLN' ? 'zł' : $this->currency;
        return number_format($this->price, 0, ',', ' ') . ' ' . $symbol;
    }

    public function getEngineVideoFileUrlAttribute(): ?string
    {
        if (!$this->engine_video_path) return null;
        if (str_starts_with($this->engine_video_path, 'http')) return $this->engine_video_path;
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->engine_video_path);
    }

    public function getTitleAttribute(): string
    {
        // Formula: Marka + Model + Silnik (engine_version) + Wersja wyposażenia.
        // Engine + trim are admin-entered free-text strings; both are
        // optional, so the title gracefully falls back to "Brand Model"
        // when the dealer hasn't filled them yet.
        $parts = array_filter([
            $this->brand?->name ?? '',
            $this->model ?? '',
            $this->engine_version ?? '',
            $this->equipment_version ?? '',
        ], fn($v) => trim((string) $v) !== '');
        return trim(implode(' ', $parts));
    }

    public function getSeoTitleAttribute(): string
    {
        if ($this->meta_title) return $this->meta_title;
        $parts = [$this->title];
        if ($this->first_registration) $parts[] = $this->first_registration;
        if ($this->mileage) $parts[] = number_format($this->mileage, 0, ',', ' ') . ' km';
        if ($this->price) $parts[] = $this->formatted_price;
        return trim(implode(' · ', array_filter($parts)));
    }

    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) return $this->meta_description;
        $bits = [];
        if ($this->first_registration) $bits[] = 'Rok ' . $this->first_registration;
        if ($this->mileage) $bits[] = 'przebieg ' . number_format($this->mileage, 0, ',', ' ') . ' km';
        if ($this->fuel_type) $bits[] = $this->fuel_type;
        if ($this->power_hp) $bits[] = $this->power_hp . ' KM';
        if ($this->transmission) $bits[] = $this->transmission;
        $tail = $bits ? ' — ' . implode(', ', $bits) . '.' : '.';
        $price = $this->price ? ' Cena: ' . $this->formatted_price . '.' : '';
        return trim("Sprawdź ofertę: {$this->title}{$tail}{$price} Certyfikowany samochód używany w CertiCars.");
    }

    public function getExistingDamagesCountAttribute(): int
    {
        return $this->damages->where('type', 'damage')->count();
    }

    public function getRepairedDamagesCountAttribute(): int
    {
        return $this->damages->where('type', 'repaired')->count();
    }

    public function getAccidentDamagesCountAttribute(): int
    {
        return $this->damages->where('type', 'accident')->count();
    }
}
