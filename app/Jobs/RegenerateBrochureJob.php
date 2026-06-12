<?php

namespace App\Jobs;

use App\Models\Car;
use App\PdfBrochure\BrochureGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Async brochure regeneration — moves Chromium PDF render out of the admin
 * save request cycle.
 *
 * Before: CarBrochureObserver::saved() called BrochureGenerationService::generate()
 * synchronously. Generating the PDF takes 3-15 SECONDS on production Chromium
 * (admin sat staring at the wizard while Browsershot rendered + uploaded to R2).
 * Switching listings to CertiCheck or editing CertiCheck rows made every save
 * feel "stuck" → users hit reload → lost form state → support tickets.
 *
 * After: observer fires this job, returns immediately. Worker picks it up and
 * regenerates the cached brochure in the background. Public download endpoint
 * still serves the cached file synchronously, so customers never wait either.
 *
 * If the queue connection is set to `sync` (e.g. local dev without a worker),
 * this falls back to inline execution — behaviour identical to the old code.
 *
 * On generation failure the service flips brochure_status='failed' itself and
 * logs the error. Job is `tries=1` because the most common failure (Chromium
 * crash on a transient image) repeats deterministically — retrying just burns
 * a worker slot. Admin-triggered regenerate (POST /admin/cars/{car}/pdf/
 * regenerate) is the recovery path.
 */
class RegenerateBrochureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Single attempt — see class docblock for rationale. */
    public int $tries = 1;

    /**
     * 90s ceiling. Chromium pre-flight ~50ms; HTML render ~500ms; PDF render
     * 3-15s for a typical CertiCheck car; storage upload ~500ms-2s. 90s gives
     * headroom for outlier cars with many images without leaving a stuck job
     * forever (which would clog the worker for the next admin save).
     */
    public int $timeout = 90;

    public function __construct(public readonly int $carId) {}

    public function handle(BrochureGenerationService $service): void
    {
        $car = Car::find($this->carId);
        if (!$car) {
            // Car was deleted between dispatch and job execution. Nothing to do.
            Log::channel('brochure')->info('pdf_brochure.job.car_missing', ['car_id' => $this->carId]);
            return;
        }

        try {
            $service->generate($car);
        } catch (\Throwable $e) {
            // Service already logged + flipped brochure_status='failed'.
            // Swallowing here mirrors the pre-async observer try/catch so a
            // QUEUE_CONNECTION=sync deploy (local dev, tests) doesn't crash
            // the admin save when Chromium is missing or transiently broken.
            Log::channel('brochure')->warning('pdf_brochure.job.regen_failed_swallowed', [
                'car_id'    => $car->id,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
        }
    }
}
