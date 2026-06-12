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
        'first_registration', 'production_year', 'mileage', 'previous_owners', 'number_of_keys',
        'fuel_type', 'power_hp', 'power_kw', 'engine_capacity', 'engine_version',
        'transmission', 'transmission_detail', 'equipment_version', 'drivetrain',
        'country_registration', 'imported_from', 'vehicle_history', 'is_imported',
        'business_use', 'taxation', 'reception_date',
        'fuel_consumption', 'fuel_procedure', 'co2_emission', 'emission_class',
        'service_book', 'service_book_status', 'service_documentation',
        'service_confirmation_type', 'odometer_status',
        'next_inspection', 'de_tech_valid_until',
        'last_service', 'last_service_mileage', 'last_service_scope',
        'registration_cert', 'owners_manual', 'coc_documents', 'vehicle_folder', 'hu_au_report',
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

        // Async via job — saving the car returns immediately to admin even
        // when the brochure regen takes 3-15s. Public download endpoint still
        // serves the cached file synchronously so customers never wait either.
        // With QUEUE_CONNECTION=sync (no worker available) the job runs inline
        // — behaviour identical to the previous synchronous call.
        \App\Jobs\RegenerateBrochureJob::dispatch($car->id);

        // Observability: log every dispatch so if customers report stuck
        // "preparing" modals we can correlate timestamps with the absence
        // of `pdf_brochure.job.started` markers — pinpointing whether the
        // bottleneck is dispatch (queue insert), worker pickup, or render.
        Log::channel('brochure')->info('pdf_brochure.observer.dispatched', [
            'car_id'  => $car->id,
            'reason'  => $needsBecauseNew ? 'new_car_or_missing_path' : 'trigger_field_changed',
            'changed' => array_values($relevantChanges),
        ]);
    }

    public function deleted(Car $car): void
    {
        // Drop the cached file when the car goes away. No status row to
        // worry about — the cars row itself is being deleted.
        $this->service->invalidate($car);
    }
}
