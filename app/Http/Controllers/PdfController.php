<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Services\BrochureImageEmbedder;
use App\Services\BrochurePdfRenderer;
use App\Services\PdfImageEmbedder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    public function generate(Car $car, BrochurePdfRenderer $browserRenderer)
    {
        $isAdmin = auth()->user()?->is_admin;

        if (($car->status !== 'active' || $car->is_sold) && !$isAdmin) {
            abort(404);
        }

        if (! $car->has_certicheck && ! $isAdmin) {
            abort(404);
        }

        $car->load('brand', 'images', 'galleryImages', 'damageImages', 'damages.photos', 'tireSets.tires');

        $reportId = (string) Str::uuid();
        $filename = 'CertiCars-' . $car->identifier . '-' . $car->slug . '.pdf';

        // Primary path: render the brochure HTML via headless Chromium.
        // The browser fetches images directly from R2 (which it does
        // reliably). We HEAD-validate every URL first so missing/non-image
        // objects never appear as broken-image icons or empty gray cells.
        try {
            $pdfContent = $this->renderViaBrowser($car, $reportId, $browserRenderer);
            $renderer   = 'browser';
        } catch (\Throwable $e) {
            // Chromium failed (missing binary, OOM, network idle timeout, etc.).
            // Fall back to DomPDF + PdfImageEmbedder so the customer still gets
            // a PDF. Log the path so we can see how often this happens.
            Log::warning('pdf.browser_failed_fallback', [
                'report_id' => $reportId,
                'car_id'    => $car->id,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
            $pdfContent = $this->renderViaDomPdf($car, $reportId);
            $renderer   = 'dompdf_fallback';
        }

        Log::info('pdf.done', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
            'renderer'  => $renderer,
            'pdf_size'  => strlen($pdfContent),
        ]);

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'X-PDF-Report-Id'     => $reportId,
            'X-PDF-Renderer'      => $renderer,
        ]);
    }

    /**
     * Render via Browsershot. Every image is fetched server-side, byte-
     * validated, resized, re-encoded as JPEG and embedded as a base64
     * data: URI directly in the HTML. Chromium never makes a network
     * request for an image at render time — that was the source of the
     * "PDF says 42 photos but the pages are blank" production bug, where
     * a HEAD-validated URL would silently fail on Chromium's actual GET.
     *
     * If an image fails to fetch / decode, pdf_src is left unset and the
     * template skips that <img> entirely. Sections whose image lists end
     * up empty are hidden by the view, so we never ship "X photos" text
     * with blank pages underneath.
     */
    private function renderViaBrowser(Car $car, string $reportId, BrochurePdfRenderer $renderer): string
    {
        $isS3       = config('filesystems.disks.public.driver') === 's3';
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;

        $embedder = new BrochureImageEmbedder($reportId, $publicBase, $isS3);

        $decorate = function (string $context) use ($embedder) {
            return function (CarImage $img) use ($embedder, $context) {
                $dataUri = $embedder->resolveToDataUri($img->path, $context);
                if ($dataUri !== null) {
                    $img->setAttribute('pdf_src', $dataUri);
                }
            };
        };

        // Hero context for primaryImage: needs higher fidelity than gallery
        // thumbnails because it renders at ~110 mm on the cover.
        if ($car->primaryImage) {
            $heroUri = $embedder->resolveToDataUri($car->primaryImage->path, 'hero');
            if ($heroUri !== null) {
                $car->primaryImage->setAttribute('pdf_src', $heroUri);
            }
        }

        $car->images->each($decorate('all_images'));
        $car->galleryImages->each($decorate('gallery'));
        $car->damageImages->each($decorate('damage_aggregate'));
        $car->damages->each(function ($d) use ($decorate, $embedder) {
            if ($d->photos) {
                $d->photos->each($decorate('damage_photo'));
            }
            $dataUri = $embedder->resolveToDataUri($d->image_path, 'damage_main');
            if ($dataUri !== null) {
                $d->setAttribute('pdf_image_src', $dataUri);
            }
        });

        Log::info('pdf.images_embedded', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
        ] + $embedder->stats());

        $html = View::make('pdf.brochure-browser', compact('car'))->render();

        return $renderer->render($html, $reportId);
    }

    /**
     * Legacy DomPDF path, kept as a safety net. Uses PdfImageEmbedder to
     * resolve every photo to a local file BEFORE handing off to DomPDF, so
     * the renderer (configured with isRemoteEnabled=false) never has to touch
     * the network. The embedder validates bytes (rejects HTML 403 pages,
     * truncated downloads, unknown formats) and converts WebP→JPEG when GD
     * supports it.
     */
    private function renderViaDomPdf(Car $car, string $reportId): string
    {
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

        Log::info('pdf.images_done', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
        ] + $embedder->stats());

        try {
            return Pdf::loadView('pdf.brochure', compact('car'))
                ->setPaper('a4')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false)
                ->setOption('isPhpEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->output();
        } finally {
            $embedder->cleanup();
        }
    }
}
