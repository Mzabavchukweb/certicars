<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\PdfBrochure\BrochureBuilder;
use App\PdfBrochure\BrochureGenerationService;
use App\PdfBrochure\ChromiumRenderer;
use App\PdfBrochure\ImageEmbedder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Endpoints for the cached CertiCheck brochure.
 *
 *  GET  /samochody/{car:slug}/pdf       — public download (CACHED ONLY)
 *  GET  /admin/cars/{car}/pdf           — same download, admin gate
 *  POST /admin/cars/{car}/pdf/regenerate — admin trigger regen
 *  GET  /admin/cars/{car}/pdf/diagnostic — image manifest, no Chromium
 *  GET  /admin/cars/pdf/health           — Chromium liveness ping
 *
 * The public download endpoint serves a pre-generated file from storage
 * ONLY. It never runs Chromium, never renders HTML, never makes a network
 * fetch. If the brochure isn't ready, the route returns a plain 404 with
 * NO attachment header so the browser surfaces a normal "Not Found" page
 * instead of saving the response body as `download.json`. This is the
 * fundamental architectural change that eliminates the failure modes
 * that produced `download.html` and `download.json` in production.
 *
 * Generation lives in BrochureGenerationService, called from:
 *   - the CarObserver, on admin save
 *   - the regenerate() admin route
 *   - the brochures:rebuild artisan command
 */
class BrochurePdfController extends Controller
{
    /**
     * Public download. Reads the cached file from the public disk and
     * streams it. Returns 404 (no attachment header) when the brochure
     * is not in 'ready' state.
     */
    public function download(Car $car)
    {
        $reportId  = (string) Str::uuid();
        $routeName = optional(request()->route())->getName() ?? 'unknown';

        $this->authorize($car);

        $ready   = $car->brochure_status === 'ready' && !empty($car->brochure_path);
        $exists  = $ready && Storage::disk('public')->exists($car->brochure_path);
        $size    = $exists ? (int) Storage::disk('public')->size($car->brochure_path) : null;

        Log::channel('brochure')->info('pdf_brochure.download.request', [
            'report_id'   => $reportId,
            'car_id'      => $car->id,
            'route'       => $routeName,
            'requested'   => request()->fullUrl(),
            'pdf_status'  => $car->brochure_status,
            'pdf_path'    => $car->brochure_path,
            'file_exists' => $exists,
            'file_size'   => $size,
            'user_admin'  => (bool) auth()->user()?->is_admin,
        ]);

        if (!$ready) {
            // Brochure is missing / generating / failed. Return a clean
            // 404 with NO Content-Disposition: attachment header. The
            // browser shows its standard "Not Found" page; the customer
            // does NOT get a saved file. This is the single root-cause
            // fix for `download.html` and `download.json`.
            abort(404);
        }

        if (!$exists) {
            // DB says ready but the file vanished — flag the car so admin
            // sees the discrepancy in the diagnostic endpoint, and return
            // 404 to the customer.
            $car->forceFill([
                'brochure_status' => 'missing',
                'brochure_path'   => null,
                'brochure_error'  => 'file missing on disk',
            ])->saveQuietly();
            Log::channel('brochure')->warning('pdf_brochure.download.file_missing_on_disk', [
                'report_id' => $reportId,
                'car_id'    => $car->id,
                'path'      => $car->brochure_path,
            ]);
            abort(404);
        }

        $pdf = Storage::disk('public')->get($car->brochure_path);
        if (!is_string($pdf) || substr($pdf, 0, 4) !== '%PDF') {
            // Cached file is corrupt. Mark and 404; admin will regen.
            $car->forceFill([
                'brochure_status' => 'failed',
                'brochure_error'  => 'cached file did not start with %PDF',
            ])->saveQuietly();
            Log::channel('brochure')->error('pdf_brochure.download.cached_bytes_invalid', [
                'report_id' => $reportId,
                'car_id'    => $car->id,
                'path'      => $car->brochure_path,
            ]);
            abort(404);
        }

        $filename = 'CertiCars-' . ($car->identifier ?? 'brochure') . '-' . $car->slug . '.pdf';

        Log::channel('brochure')->info('pdf_brochure.download.served', [
            'report_id'    => $reportId,
            'car_id'       => $car->id,
            'pdf_size'     => strlen($pdf),
            'content_type' => 'application/pdf',
        ]);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Length'      => (string) strlen($pdf),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'X-PDF-Report-Id'     => $reportId,
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }

    /**
     * Admin-only: kick off a regenerate for one car. Synchronous — admin
     * accepts a few seconds wait so the page comes back with the new
     * state immediately. On success: redirect back with flash; on failure:
     * 500 JSON with the exception class.
     */
    public function regenerate(Car $car, BrochureGenerationService $svc)
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }
        try {
            $svc->generate($car);
            return back()->with('ok', 'Brochure regenerated for ' . $car->slug);
        } catch (\Throwable $e) {
            return response()->json([
                'error'     => 'regenerate_failed',
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'car_id'    => $car->id,
                'status'    => $car->fresh()->brochure_status,
                'last_error'=> $car->fresh()->brochure_error,
            ], 500);
        }
    }

    /**
     * Admin-only: build BrochureData + return embedder manifest as JSON
     * without invoking Chromium. Use to debug why a regen produces an
     * unexpected output.
     */
    public function diagnostic(Car $car): JsonResponse
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }

        $reportId = (string) Str::uuid();
        $builder  = $this->newBuilder($reportId);
        [$data, $embedder] = $builder->build($car, $reportId);

        return response()->json([
            'report_id' => $reportId,
            'car_id'    => $car->id,
            'car_slug'  => $car->slug,
            'brochure'  => [
                'status'       => $car->brochure_status,
                'path'         => $car->brochure_path,
                'size'         => $car->brochure_size,
                'generated_at' => optional($car->brochure_generated_at)->toIso8601String(),
                'error'        => $car->brochure_error,
                'file_exists'  => $car->brochure_path
                    ? Storage::disk('public')->exists($car->brochure_path)
                    : null,
            ],
            'stats'    => $embedder->stats(),
            'summary'  => [
                'hero_embedded'            => $data->heroImage !== null,
                'gallery_embedded'         => count($data->galleryImages),
                'damage_embedded'          => count($data->damageImages),
                'damage_cards_with_photos' => count(array_filter($data->damages, fn ($d) => count($d['photos']) > 0)),
                'tire_set_count'           => count($data->tireSets),
                'tech_condition_count'     => count($data->technicalConditions),
                'paint_row_count'          => count($data->paintMeasurements),
                'equipment_categories'     => count($data->equipment),
            ],
            'manifest' => $embedder->manifest(),
        ]);
    }

    /**
     * Admin-only Chromium liveness check. Hit after a deploy that touches
     * the Dockerfile to confirm the binary is in place before customers
     * report broken regens.
     */
    public function health(ChromiumRenderer $renderer): JsonResponse
    {
        if (!auth()->user()?->is_admin) {
            abort(403);
        }
        $reportId = (string) Str::uuid();
        try {
            $renderer->assertReady($reportId);
            return response()->json(['ok' => true, 'report_id' => $reportId]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'        => false,
                'report_id' => $reportId,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'env_path'  => env('PUPPETEER_EXECUTABLE_PATH'),
            ], 503);
        }
    }

    private function newBuilder(string $reportId): BrochureBuilder
    {
        $isS3       = config('filesystems.disks.public.driver') === 's3';
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;

        return new BrochureBuilder(
            new ImageEmbedder($reportId, $publicBase, $isS3)
        );
    }

    private function authorize(Car $car): void
    {
        $isAdmin = auth()->user()?->is_admin;
        if (($car->status !== 'active' || $car->is_sold) && !$isAdmin) {
            abort(404);
        }
        if (!$car->has_certicheck && !$isAdmin) {
            abort(404);
        }
    }
}
