<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CarImage extends Model
{
    protected $fillable = ['car_id', 'damage_id', 'path', 'type', 'alt_text', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function getAltAttribute(): string
    {
        if (!empty($this->alt_text)) return $this->alt_text;
        $title = $this->car?->title ?? 'Samochód';
        $typeLabel = ['gallery' => 'zdjęcie', 'damage' => 'zdjęcie uszkodzenia', 'exterior' => 'zdjęcie', 'interior' => 'wnętrze'][$this->type] ?? 'zdjęcie';
        return trim($title . ' — ' . $typeLabel);
    }

    public function getUrlAttribute(): string
    {
        if (!$this->path) {
            return asset('images/placeholder-car.svg');
        }
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        // For S3/R2, skip the existence check entirely — files are durable and each HeadObject
        // call adds latency plus R2 may return auth errors that cause 504 on heavy pages.
        // For local disk, check existence to detect files lost after a redeploy.
        if (config('filesystems.disks.public.driver') !== 's3') {
            try {
                if (!Storage::disk('public')->exists($this->path)) {
                    return asset('images/placeholder-car.svg');
                }
            } catch (\Throwable) {
                return asset('images/placeholder-car.svg');
            }
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($this->path);
    }
}
