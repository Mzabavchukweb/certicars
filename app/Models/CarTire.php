<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarTire extends Model
{
    protected $fillable = ['car_tire_set_id', 'position', 'tread_depth', 'condition'];

    protected $casts = [
        'condition' => 'array',
    ];

    public function set(): BelongsTo
    {
        return $this->belongsTo(CarTireSet::class, 'car_tire_set_id');
    }
}
