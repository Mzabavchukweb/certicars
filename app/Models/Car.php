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
        'first_registration', 'mileage', 'previous_owners', 'business_use', 'number_of_keys',
        'fuel_type', 'power_hp', 'power_kw', 'engine_capacity', 'transmission', 'transmission_detail',
        'location', 'location_distance', 'source', 'is_imported', 'country_registration', 'taxation',
        'last_service', 'last_service_mileage', 'next_inspection', 'service_documentation',
        'fuel_consumption', 'fuel_procedure', 'co2_emission', 'emission_class',
        'service_book', 'coc_documents', 'vehicle_folder', 'hu_au_report',
        'paint_measurements', 'technical_conditions', 'equipment', 'engine_video_url', 'engine_video_path',
        'is_featured', 'is_sold', 'has_certicheck', 'available_now', 'home_delivery', 'has_gethelp', 'gethelp_package', 'status',
        'meta_title', 'meta_description', 'focus_keyword', 'noindex',
    ];

    protected $casts = [
        'paint_measurements' => 'array',
        'technical_conditions' => 'array',
        'equipment' => 'array',
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
    ];

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
        return trim(($this->brand?->name ?? '') . ' ' . $this->model);
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
