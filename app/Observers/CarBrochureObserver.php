<?php

namespace App\Observers;

use App\Models\Car;
use App\PdfBrochure\BrochureGenerationService;
use Illuminate\Support\Facades\Log;

/**
 * Keeps each car's cached CertiCheck brochure in lockstep with its data.
 *
 * Triggered on Eloquent `saved` (creating + updating fire that event). The
 * observer decides whether the change actually affects the brochure and
 * only re-runs generation when it does — toggling is_featured or marking
 * sold doesn't justify a 10-second Chromium spin.
 *
 * Recursion safety: BrochureGenerationService writes the status columns
 * via saveQuietly(), which skips this observer. No infinite loop.
 *
 * Errors are swallowed at this layer — admin save must not fail because
 * the brochure regen ran into a transient issue. The car row is correct;
 * the brochure_status column reflects the failure (via the service).
 */
class CarBrochureObserver
{
    /**
     * Fields whose change should trigger a brochure regeneration. Catalog-
     * only flags (is_featured, is_sold, status, noindex, meta_*) are
     * deliberately absent — they don't appear on the report.
     */
    private const TRIGGER_FIELDS = [
        'has_certicheck',
        'brand_id', 'model', 'category', 'identifier', 'price', 'currency',
        'color', 'color_code', 'doors', 'seats', 'weight', 'upholstery', 'vin', 'body_type',
        'first_registration', 'mileage', 'previous_owners', 'number_of_keys',
        'fuel_type', 'power_hp', 'power_kw', 'engine_capacity',
        'transmission', 'transmission_detail', 'equipment_version', 'drivetrain',
        'country_registration', 'imported_from', 'vehicle_history',
        'fuel_consumption', 'fuel_procedure', 'co2_emission', 'emission_class',
        'service_book_status', 'registration_cert', 'owners_manual',
        'aso_serviced', 'service_history',
        'paint_measurements', 'technical_conditions',
        'equipment', 'highlighted_equipment',
        'engine_video_url', 'engine_video_path',
    ];

    public function __construct(
        private readonly BrochureGenerationService $service,
    ) {}

    public function saved(Car $car): void
    {
        // No brochure for cars without CertiCheck.
        if (!$car->has_certicheck) {
            if ($car->brochure_status !== 'missing' || $car->brochure_path !== null) {
                $this->service->invalidate($car);
            }
            return;
        }

        // Brand new car with CertiCheck (no path yet) → generate.
        $needsBecauseNew = $car->wasRecentlyCreated || $car->brochure_path === null;

        $relevantChanges = array_filter(
            self::TRIGGER_FIELDS,
            fn (string $f) => $car->wasChanged($f),
        );

        if (!$needsBecauseNew && count($relevantChanges) === 0) {
            return; // nothing the brochure cares about changed
        }

        try {
            $this->service->generate($car);
        } catch (\Throwable $e) {
            // Service already logged + flipped brochure_status to 'failed'.
            // Don't rethrow — admin save must not bounce because of a
            // transient Chromium issue.
            Log::warning('pdf_brochure.observer.regen_failed_swallowed', [
                'car_id'    => $car->id,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'changed'   => array_values($relevantChanges),
            ]);
        }
    }

    public function deleted(Car $car): void
    {
        // Drop the cached file when the car goes away. No status row to
        // worry about — the cars row itself is being deleted.
        $this->service->invalidate($car);
    }
}
