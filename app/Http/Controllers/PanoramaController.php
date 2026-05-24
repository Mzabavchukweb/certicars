<?php

namespace App\Http\Controllers;

use App\Models\CarImage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PanoramaController extends Controller
{
    public function stream(CarImage $carImage): StreamedResponse
    {
        if (!in_array($carImage->type, ['pano360', 'pano360ext'], true)) {
            abort(404);
        }

        $car = $carImage->car;
        if (!$car || $car->status !== 'active') {
            abort(404);
        }

        $path = $carImage->path;

        // Full URL stored — redirect so the browser fetches directly.
        // This only happens for legacy http-prefixed paths; R2 paths are relative.
        if (str_starts_with($path, 'http')) {
            return redirect($path);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        $mime = $disk->mimeType($path) ?: 'image/jpeg';
        $size = $disk->size($path);

        return response()->stream(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'   => $mime,
            'Content-Length' => $size,
            'Cache-Control'  => 'public, max-age=86400',
        ]);
    }
}
