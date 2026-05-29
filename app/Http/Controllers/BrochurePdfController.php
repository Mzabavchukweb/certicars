<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\PdfBrochure\BrochureBuilder;
use App\PdfBrochure\ChromiumRenderer;
use App\PdfBrochure\ImageEmbedder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Public + admin endpoints for the CertiCheck brochure.
 *
 *  GET /samochody/{car:slug}/pdf       — public download
 *  GET /admin/cars/{car}/pdf           — admin gate
 *  GET /admin/cars/{car}/pdf/diagnostic — JSON manifest, no Chromium
 *
 * Failure model (rewritten after the "download.html" production incident):
 *  - Every step (auth, build, view, render) is wrapped in one master
 *    try/catch that converts ANY throwable into a clean JSON 500 with the
 *    report_id baked in. The response NEVER carries a
 *    Content-Disposition: attachment header on the error path, so the
 *    browser shows the JSON in-tab instead of saving it as `download.html`.
 *  - assertReady() pre-checks the Chromium binary so we fail in ~1 ms
 *    instead of after the 60-second Browsershot timeout.
 *  - Each step emits a structured Log::info so a Railway log filter on
 *    the report_id gives a complete timeline of one request.
 */
class BrochurePdfController extends Controller
{
    public function generate(Car $car, ChromiumRenderer $renderer)
    {
        $reportId = (string) Str::uuid();
        $routeName = optional(request()->route())->getName() ?? 'unknown';
        $tStart = microtime(true);

        Log::info('pdf_brochure.request', [
            'report_id'  => $reportId,
            'car_id'     => $car->id,
            'route'      => $routeName,
            'user_admin' => (bool) auth()->user()?->is_admin,
        ]);

        try {
            $this->authorize($car);

            // Pre-flight: confirm Chromium can be spawned before we burn 30
            // seconds on image fetches. Fails in single-digit milliseconds
            // if the binary is missing.
            $renderer->assertReady($reportId);

            $builder = $this->newBuilder($reportId);

            Log::info('pdf_brochure.build_start', ['report_id' => $reportId]);
            [$data, $embedder] = $builder->build($car, $reportId);
            Log::info('pdf_brochure.build_done', [
                'report_id' => $reportId,
            ] + $embedder->stats());

            Log::info('pdf_brochure.view_start', ['report_id' => $reportId]);
            $html = View::make('pdf-brochure.document', ['b' => $data])->render();
            Log::info('pdf_brochure.view_done', [
                'report_id'    => $reportId,
                'html_length'  => strlen($html),
            ]);

            Log::info('pdf_brochure.chromium_start', ['report_id' => $reportId]);
            $pdf = $renderer->render($html, $reportId);
            // pdf_brochure.chromium_ok logged inside the renderer.

            // Final guard: refuse to emit anything that doesn't actually
            // start with %PDF, even if the renderer thought it succeeded.
            if (!is_string($pdf) || substr($pdf, 0, 4) !== '%PDF') {
                throw new \RuntimeException('PDF output did not start with %PDF — refusing to send.');
            }

            $filename = 'CertiCars-' . ($car->identifier ?? 'brochure') . '-' . $car->slug . '.pdf';
            $durationMs = (int) ((microtime(true) - $tStart) * 1000);

            Log::info('pdf_brochure.done', [
                'report_id'   => $reportId,
                'car_id'      => $car->id,
                'pdf_size'    => strlen($pdf),
                'duration_ms' => $durationMs,
                'content_type'=> 'application/pdf',
            ] + $embedder->stats());

            return response($pdf, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Length'      => (string) strlen($pdf),
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'X-PDF-Report-Id'     => $reportId,
                // Tell every proxy + browser cache to never cache this
                // response — re-generating cheap, accidentally serving a
                // stale broken PDF expensive.
                'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'              => 'no-cache',
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // 404 from authorize() is a legitimate not-found. Re-throw so
            // Laravel returns the normal 404 page (NOT an attachment).
            throw $e;
        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $tStart) * 1000);

            Log::error('pdf_brochure.failed', [
                'report_id'   => $reportId,
                'car_id'      => $car->id,
                'route'       => $routeName,
                'exception'   => get_class($e),
                'message'     => $e->getMessage(),
                'trace_head'  => $this->trimTrace($e),
                'duration_ms' => $durationMs,
            ]);

            // CRITICAL: never send Content-Disposition: attachment on the
            // error path. Browsers in download mode save the response body
            // verbatim — that's how customers ended up with `download.html`
            // files in production. JSON content-type + no attachment
            // header → browser navigates to the response in a tab and the
            // user can see the report_id to paste back to support.
            return response()->json([
                'error'     => 'PDF generation failed.',
                'message'   => 'Spróbuj ponownie za chwilę lub skontaktuj się z nami podając poniższy identyfikator.',
                'report_id' => $reportId,
            ], 500, [
                'Content-Type'    => 'application/json; charset=utf-8',
                'X-PDF-Report-Id' => $reportId,
                'Cache-Control'   => 'no-store',
            ]);
        }
    }

    /**
     * Admin-only: build BrochureData + return embedder manifest as JSON
     * without invoking Chromium. Quickest way to determine which images
     * were skipped and why for a specific car.
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
            'stats'     => $embedder->stats(),
            'summary'   => [
                'hero_embedded'             => $data->heroImage !== null,
                'gallery_embedded'          => count($data->galleryImages),
                'damage_embedded'           => count($data->damageImages),
                'damage_cards_with_photos'  => count(array_filter($data->damages, fn ($d) => count($d['photos']) > 0)),
                'tire_set_count'            => count($data->tireSets),
                'tech_condition_count'      => count($data->technicalConditions),
                'paint_row_count'           => count($data->paintMeasurements),
                'equipment_categories'      => count($data->equipment),
            ],
            'manifest' => $embedder->manifest(),
        ]);
    }

    /**
     * Admin-only Chromium liveness check. Useful when a deploy has
     * shipped and you want to confirm the binary is actually present
     * before customers start complaining about broken downloads.
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
                'ok'         => false,
                'report_id'  => $reportId,
                'exception'  => get_class($e),
                'message'    => $e->getMessage(),
                'env_path'   => env('PUPPETEER_EXECUTABLE_PATH'),
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

    /** First few frames of the stack trace — full trace is too large for log. */
    private function trimTrace(\Throwable $e): array
    {
        $frames = explode("\n", $e->getTraceAsString());
        return array_slice($frames, 0, 8);
    }
}
