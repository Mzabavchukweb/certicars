<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarTireSet extends Model
{
    protected $fillable = ['car_id', 'set_number', 'is_mounted', 'tire_type', 'rim', 'notes'];

    protected $casts = [
        'is_mounted' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function tires(): HasMany
    {
        return $this->hasMany(CarTire::class);
    }
}
