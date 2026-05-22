<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarDamage extends Model
{
    protected $fillable = [
        'car_id', 'area', 'severity', 'type', 'tags',
        'description', 'position_x', 'position_y', 'position_view', 'image_path',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
    }

    protected $casts = [
        'tags' => 'array',
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CarImage::class, 'damage_id')
            ->orderBy('sort_order');
    }
}
