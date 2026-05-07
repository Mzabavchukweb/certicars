<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generate(Car $car)
    {
        if (($car->status !== 'active' || $car->is_sold) && !(auth()->user()?->is_admin)) {
            abort(404);
        }

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages', 'tireSets.tires');

        $pdf = Pdf::loadView('pdf.brochure', compact('car'))
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf');
    }
}
