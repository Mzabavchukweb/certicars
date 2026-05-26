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

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires');

        // Convert image paths to local filesystem paths for dompdf (isRemoteEnabled=false prevents SSRF).
        // For S3/R2, download each image to a temp file; clean up after the PDF is built.
        // Cache by path so the same physical file isn't fetched twice when it appears in
        // multiple relations (images / galleryImages / damageImages / damages.photos).
        $tmpFiles = [];
        $isS3 = config('filesystems.disks.public.driver') === 's3';
        $pathCache = [];

        $decorate = function (CarImage $img) use ($isS3, &$tmpFiles, &$pathCache) {
            if (!$img->path || str_starts_with($img->path, 'http')) {
                return;
            }
            if (isset($pathCache[$img->path])) {
                $img->setAttribute('pdf_src', $pathCache[$img->path]);
                return;
            }
            try {
                if (!Storage::disk('public')->exists($img->path)) {
                    return;
                }
                if ($isS3) {
                    $tmp = tempnam(sys_get_temp_dir(), 'certicars_pdf_');
                    file_put_contents($tmp, Storage::disk('public')->get($img->path));
                    $img->setAttribute('pdf_src', $tmp);
                    $pathCache[$img->path] = $tmp;
                    $tmpFiles[] = $tmp;
                } else {
                    $local = Storage::disk('public')->path($img->path);
                    $img->setAttribute('pdf_src', $local);
                    $pathCache[$img->path] = $local;
                }
            } catch (\Throwable) {
                // Silently skip — the view falls back to $img->url then a placeholder.
            }
        };

        // Eloquent relations are separate in-memory collections, so each one must be
        // decorated individually for pdf_src to surface in every Blade loop.
        $car->images->each($decorate);
        $car->galleryImages->each($decorate);
        $car->damageImages->each($decorate);
        // Per-damage photos linked through CarDamage::photos() (PR #7).
        $car->damages->each(function ($d) use ($decorate) {
            if ($d->photos) {
                $d->photos->each($decorate);
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
