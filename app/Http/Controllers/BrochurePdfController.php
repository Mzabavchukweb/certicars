<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\PdfBrochure\BrochureBuilder;
use App\PdfBrochure\BrochureRenderException;
use App\PdfBrochure\ChromiumRenderer;
use App\PdfBrochure\ImageEmbedder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Public + admin endpoints for the CertiCheck brochure.
 *
 *  GET /samochody/{car:slug}/pdf     — public download
 *  GET /admin/cars/{car}/pdf         — same generator, admin gate
 *  GET /admin/cars/{car}/pdf/diagnostic — JSON manifest of what would be
 *                                         embedded (admin-only, no PDF
 *                                         rendered). Use this to figure out
 *                                         why a real prod brochure shipped
 *                                         empty without having to dig
 *                                         through Railway logs.
 *
 * There is NO DomPDF fallback. If Chromium fails we throw a 500 — the old
 * fallback pipeline was unreliable and shipping a broken PDF in its place
 * was worse than failing loudly.
 */
class BrochurePdfController extends Controller
{
    public function generate(Car $car, ChromiumRenderer $renderer)
    {
        $this->authorize($car);

        $reportId = (string) Str::uuid();
        $builder  = $this->newBuilder($reportId);

        [$data, $embedder] = $builder->build($car, $reportId);

        $html = View::make('pdf-brochure.document', ['b' => $data])->render();

        try {
            $pdf = $renderer->render($html, $reportId);
        } catch (BrochureRenderException $e) {
            Log::error('pdf_brochure.render_failed', [
                'report_id' => $reportId,
                'car_id'    => $car->id,
                'message'   => $e->getMessage(),
            ]);
            abort(500, 'Brochure rendering failed. Please try again or contact support.');
        }

        Log::info('pdf_brochure.done', [
            'report_id' => $reportId,
            'car_id'    => $car->id,
            'pdf_size'  => strlen($pdf),
        ] + $embedder->stats());

        $filename = 'CertiCars-' . ($car->identifier ?? 'brochure') . '-' . $car->slug . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'X-PDF-Report-Id'     => $reportId,
        ]);
    }

    /**
     * Admin-only: build the BrochureData and return the embedder manifest
     * as JSON without actually invoking Chromium. Use this to determine
     * which images were skipped and why for a specific car.
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
                'hero_embedded'        => $data->heroImage !== null,
                'gallery_embedded'     => count($data->galleryImages),
                'damage_embedded'      => count($data->damageImages),
                'damage_cards_with_photos' => count(array_filter($data->damages, fn ($d) => count($d['photos']) > 0)),
                'tire_set_count'       => count($data->tireSets),
                'tech_condition_count' => count($data->technicalConditions),
                'paint_row_count'      => count($data->paintMeasurements),
                'equipment_categories' => count($data->equipment),
            ],
            'manifest' => $embedder->manifest(),
        ]);
    }

    private function newBuilder(string $reportId): BrochureBuilder
    {
        $isS3       = config('filesystems.disks.public.driver') === 's3';
        $publicBase = $isS3 ? rtrim((string) config('filesystems.disks.public.url'), '/') : null;

        return new BrochureBuilder(
            new ImageEmbedder($reportId, $publicBase, $isS3)
        );
    }

    /**
     * Public view gate: car must be active + not sold + has CertiCheck, OR
     * the request comes from an admin. Same rules the old PdfController
     * applied — there is no business reason to change this.
     */
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
