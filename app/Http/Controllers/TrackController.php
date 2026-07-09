<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Event;
use App\Support\Analytics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Odbiornik zdarzeń z przeglądarki (navigator.sendBeacon → POST /zdarzenie).
 *
 * Zaufanie: żadne. Nazwa zdarzenia musi być na liście Analytics::CLIENT_EVENTS,
 * `meta` jest przycinane, a auto identyfikujemy po slugu — klient nie podaje
 * surowego car_id. Zdarzenia serwerowe (pdf_download, inquiry_submitted...)
 * są tu jawnie ODRZUCANE, żeby nikt nie napompował sobie konwersji.
 */
class TrackController extends Controller
{
    private const MAX_META_KEYS = 5;

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:64'],
            'slug'   => ['nullable', 'string', 'max:200'],
            // Ulubione przełącza się z listy katalogu, gdzie w DOM jest id, nie slug.
            'car_id' => ['nullable', 'integer', 'exists:cars,id'],
            'meta'   => ['nullable', 'array'],
        ]);

        if (!in_array($data['name'], Analytics::CLIENT_EVENTS, true)) {
            return response()->json(['ok' => false], 422);
        }

        $carId = $data['car_id'] ?? null;
        if (!$carId && !empty($data['slug'])) {
            $carId = Car::where('slug', $data['slug'])->value('id');
        }

        Event::record($data['name'], $request, $carId, $this->sanitizeMeta($data['meta'] ?? []));

        return response()->json(['ok' => true]);
    }

    /** Skalary tylko, max 5 kluczy, wartości do 120 znaków. */
    private function sanitizeMeta(array $meta): array
    {
        $clean = [];

        foreach ($meta as $key => $value) {
            if (count($clean) >= self::MAX_META_KEYS) break;
            if (!is_scalar($value) && !is_null($value)) continue;

            $clean[mb_substr((string) $key, 0, 40)] = is_string($value)
                ? mb_substr($value, 0, 120)
                : $value;
        }

        return $clean;
    }
}
