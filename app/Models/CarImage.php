<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CarImage extends Model
{
    protected $fillable = ['car_id', 'path', 'type', 'alt_text', 'is_primary', 'sort_order'];

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
        $siblings = $this->car?->images()->where('type', $this->type)->orderBy('sort_order')->pluck('id')->values() ?? collect();
        $idx = $siblings->search($this->id);
        $typeLabel = ['gallery' => 'zdjęcie', 'damage' => 'zdjęcie uszkodzenia', 'exterior' => 'zdjęcie', 'interior' => 'wnętrze'][$this->type] ?? 'zdjęcie';
        return trim($title . ' — ' . $typeLabel . ($idx !== false ? ' ' . ($idx + 1) : ''));
    }

    public function getUrlAttribute(): string
    {
        if (!$this->path) {
            return asset('images/placeholder-car.svg');
        }
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        if (!Storage::disk('public')->exists($this->path)) {
            return asset('images/placeholder-car.svg');
        }
        return asset('storage/' . $this->path);
    }
}
