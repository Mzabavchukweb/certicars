<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function generate(Car $car)
    {
        $isAdmin = auth()->user()?->is_admin;

        if (($car->status !== 'active' || $car->is_sold) && !$isAdmin) {
            abort(404);
        }

        if (! $car->has_certicheck && ! $isAdmin) {
            abort(404);
        }

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires');

        // Convert all image URLs to local disk paths to prevent SSRF via dompdf
        $car->images->each(function (CarImage $img) {
            if (!str_starts_with($img->path, 'http') && Storage::disk('public')->exists($img->path)) {
                $img->setAttribute('pdf_src', Storage::disk('public')->path($img->path));
            }
        });

        $pdf = Pdf::loadView('pdf.brochure', compact('car'))
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf');
    }
}
