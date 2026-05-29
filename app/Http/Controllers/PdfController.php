<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Services\BrochureImageValidator;
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
     * Render via Browsershot. Every image URL is HEAD-validated before being
     * passed to the template — unreachable objects are silently dropped so
     * the brochure HTML carries only confirmed-good <img> tags.
     */
    private function renderViaBrowser(Car $car, string $reportId, BrochurePdfRenderer $renderer): string
    {
        $isS3       = config('filesystems.disks.public.driver') === 's3';
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;

        $validator = new BrochureImageValidator($reportId, $publicBase, $isS3);

        // Local-disk dev fallback: emit data: URIs (base64-encoded image
        // bytes) so Chromium can render the assets without needing any
        // network round trip. Browsershot hardcodes a refusal to render
        // HTML that references localhost/127.x/file:// URLs (security
        // policy with no override). Data URIs slip past that check and
        // also avoid the `php artisan serve` single-process deadlock where
        // Chromium can't fetch storage URLs while the same PHP worker is
        // blocked waiting for Chromium. In production this branch is
        // never taken (isS3 == true), so the URLs that hit Chromium are
        // R2 HTTPS URLs and no base64 encoding happens.
        $resolveLocal = function (string $path): ?string {
            if ($path === '') return null;
            try {
                $disk = \Illuminate\Support\Facades\Storage::disk('public');
                if (!$disk->exists($path)) return null;
                $full  = $disk->path($path);
                $bytes = @file_get_contents($full);
                if ($bytes === false || $bytes === '') return null;
                $mime = @mime_content_type($full) ?: 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode($bytes);
            } catch (\Throwable) {
                return null;
            }
        };

        $decorate = function (string $context) use ($validator, $resolveLocal, $isS3) {
            return function (CarImage $img) use ($validator, $resolveLocal, $context, $isS3) {
                $url = $validator->resolveToPublicUrl($img->path, $context);
                if ($url === null && !$isS3) {
                    $url = $resolveLocal((string) $img->path);
                }
                if ($url !== null) {
                    $img->setAttribute('pdf_src', $url);
                }
            };
        };

        $car->images->each($decorate('all_images'));
        $car->galleryImages->each($decorate('gallery'));
        $car->damageImages->each($decorate('damage_aggregate'));
        $car->damages->each(function ($d) use ($decorate, $validator, $resolveLocal, $isS3) {
            if ($d->photos) {
                $d->photos->each($decorate('damage_photo'));
            }
            $url = $validator->resolveToPublicUrl($d->image_path, 'damage_main');
            if ($url === null && !$isS3) {
                $url = $resolveLocal((string) $d->image_path);
            }
            if ($url !== null) {
                $d->setAttribute('pdf_image_src', $url);
            }
        });

        Log::info('pdf.images_validated', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
        ] + $validator->stats());

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
