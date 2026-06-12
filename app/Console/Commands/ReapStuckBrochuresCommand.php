<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sweep cars whose brochure status has been stuck in `generating` for longer
 * than the job's own timeout — i.e. the worker died mid-render (Chromium
 * crash, OOM, container restart) without flipping the column to `failed`.
 *
 * Without this command, those rows stay `generating` forever and the
 * customer-facing CTA shows the premium "preparing" modal indefinitely. The
 * RegenerateBrochureJob has `timeout=90`s; we wait 10 minutes before
 * declaring an orphan to leave plenty of headroom for legitimate slow
 * renders + queue backlog.
 *
 * Recovery on the next admin save: when the row gets touched again, the
 * observer dispatches a fresh job (`brochure_path === null` triggers
 * `$needsBecauseNew`). Admin can also click the "Regenerate" route.
 *
 * Schedule: every 5 minutes via routes/console.php.
 */
class ReapStuckBrochuresCommand extends Command
{
    protected $signature = 'brochures:reap-stuck
        {--minutes=10 : Mark cars stuck in `generating` longer than this as `failed`}
        {--dry-run : Print what would happen without writing}';

    protected $description = 'Mark orphaned brochure rows (status=generating but worker died) as failed';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        if ($minutes < 1) $minutes = 10;
        $dry = (bool) $this->option('dry-run');

        $cutoff = now()->subMinutes($minutes);

        $orphans = Car::query()
            ->where('brochure_status', 'generating')
            ->where('updated_at', '<', $cutoff)
            ->get(['id', 'slug', 'brochure_status', 'updated_at']);

        if ($orphans->isEmpty()) {
            $this->info('No stuck brochures.');
            return self::SUCCESS;
        }

        $this->warn("Found {$orphans->count()} stuck brochure(s) older than {$minutes}m:");
        foreach ($orphans as $car) {
            $this->line("  #{$car->id} {$car->slug} (stuck since {$car->updated_at})");
        }

        if ($dry) {
            $this->comment('Dry-run mode — no changes written.');
            return self::SUCCESS;
        }

        foreach ($orphans as $car) {
            // saveQuietly to skip CarBrochureObserver — we don't want this
            // sweep to re-dispatch a fresh job that would likely fail for
            // the same underlying reason (e.g. Chromium broken). Admin must
            // explicitly hit the regenerate route or re-save the car.
            $car->forceFill([
                'brochure_status' => 'failed',
                'brochure_error'  => 'Worker did not complete within ' . $minutes . ' minutes (orphan sweep).',
            ])->saveQuietly();

            Log::warning('pdf_brochure.reap.orphan_marked_failed', [
                'car_id'        => $car->id,
                'stuck_minutes' => $car->updated_at?->diffInMinutes(now()),
            ]);
        }

        $this->info("Marked {$orphans->count()} stuck brochure(s) as failed.");
        return self::SUCCESS;
    }
}
