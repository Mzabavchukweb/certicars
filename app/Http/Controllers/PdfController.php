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

        // Convert image paths to local filesystem paths for dompdf (isRemoteEnabled=false prevents SSRF).
        // For S3/R2, download each image to a temp file; clean up after the PDF is built.
        $tmpFiles = [];
        $isS3 = config('filesystems.disks.public.driver') === 's3';

        $car->images->each(function (CarImage $img) use ($isS3, &$tmpFiles) {
            if (str_starts_with($img->path, 'http') || !Storage::disk('public')->exists($img->path)) {
                return;
            }
            if ($isS3) {
                $tmp = tempnam(sys_get_temp_dir(), 'certicars_pdf_');
                file_put_contents($tmp, Storage::disk('public')->get($img->path));
                $img->setAttribute('pdf_src', $tmp);
                $tmpFiles[] = $tmp;
            } else {
                $img->setAttribute('pdf_src', Storage::disk('public')->path($img->path));
            }
        });

        $filename = 'CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf';

        $pdfContent = Pdf::loadView('pdf.brochure', compact('car'))
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->output();

        foreach ($tmpFiles as $tmp) {
            @unlink($tmp);
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
