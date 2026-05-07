<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarView extends Model
{
    public $timestamps = false;

    protected $fillable = ['car_id', 'session_id', 'ip', 'referer', 'user_agent', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
