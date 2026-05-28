<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Services\PdfImageEmbedder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        // Embedder resolves every photo to a local file BEFORE we hand off to
        // DomPDF, so the renderer with isRemoteEnabled=false never has to
        // touch a network. The service does bytes validation (rejects HTML
        // 403 pages, truncated downloads, unknown formats) and WebP → JPEG
        // conversion when GD is available. Anything that fails any check is
        // returned as null and the template skips it cleanly — no broken-X.
        $reportId   = (string) Str::uuid();
        $isS3       = config('filesystems.disks.public.driver') === 's3';
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;

        $embedder = new PdfImageEmbedder($reportId, $publicBase, $isS3);

        $decorate = function (string $context) use ($embedder) {
            return function (CarImage $img) use ($embedder, $context) {
                $local = $embedder->resolveToLocalFile($img->path, $context);
                if ($local !== null) {
                    $img->setAttribute('pdf_src', $local);
                }
            };
        };

        $car->images->each($decorate('all_images'));
        $car->galleryImages->each($decorate('gallery'));
        $car->damageImages->each($decorate('damage_aggregate'));
        $car->damages->each(function ($d) use ($decorate, $embedder) {
            if ($d->photos) {
                $d->photos->each($decorate('damage_photo'));
            }
            $local = $embedder->resolveToLocalFile($d->image_path, 'damage_main');
            if ($local !== null) {
                $d->setAttribute('pdf_image_src', $local);
            }
        });

        Log::info('pdf.images_done', ['report_id' => $reportId, 'car_id' => $car->id] + $embedder->stats());

        $filename = 'CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf';

        try {
            $pdfContent = Pdf::loadView('pdf.brochure', compact('car'))
                ->setPaper('a4')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false)
                // Enable the in-template <script type="text/php"> callback that stamps
                // page numbers; the brochure view sources only trusted server strings
                // so no user input crosses this boundary.
                ->setOption('isPhpEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->output();
        } finally {
            $embedder->cleanup();
        }

        Log::info('pdf.done', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
            'pdf_size'  => strlen($pdfContent),
        ]);

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            // Echo the trace id so we can correlate a downloaded file with logs
            // if a customer reports a broken PDF after deploy.
            'X-PDF-Report-Id'     => $reportId,
        ]);
    }
}
