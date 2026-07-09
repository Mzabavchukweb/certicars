<?php

namespace App\Models;

use App\Support\Analytics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Event extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'car_id', 'session_id', 'visitor_id', 'path', 'referer',
        'utm_source', 'utm_medium', 'utm_campaign', 'device', 'meta', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'meta'       => 'array',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function getLabelAttribute(): string
    {
        return Analytics::eventLabel($this->name);
    }

    /**
     * Zapisuje zdarzenie z kontekstem requestu. Nie rzuca wyjątkiem — pomiar
     * nigdy nie może wywrócić strony. Zwraca false, gdy pominięto (bot).
     */
    public static function record(string $name, Request $request, ?int $carId = null, array $meta = []): bool
    {
        if (Analytics::isBot($request->userAgent())) {
            return false;
        }

        $payload = [
            'name'       => $name,
            'car_id'     => $carId,
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'visitor_id' => Analytics::existingVisitorId($request),
            'path'       => mb_substr($request->path(), 0, 500),
            'referer'    => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
            'device'     => Analytics::device($request->userAgent()),
            'meta'       => $meta ?: null,
            'created_at' => now(),
        ] + Analytics::utm($request);

        defer(function () use ($payload) {
            try {
                static::create($payload);
            } catch (\Throwable) {
                // Pomiar nie może wywrócić żądania.
            }
        });

        return true;
    }
}
