<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\PdfBrochure\BrochureGenerationService;
use Illuminate\Console\Command;

/**
 * Backfill / ops command for the cached CertiCheck brochure.
 *
 *   php artisan brochures:rebuild              — every CertiCheck car
 *   php artisan brochures:rebuild --missing    — only ones not in 'ready'
 *   php artisan brochures:rebuild --car=42     — single car by id
 *
 * Designed to be run once after the cached-brochure migration deploys so
 * existing customers get a working download immediately, without waiting
 * for an admin to re-save every car.
 */
class RebuildBrochuresCommand extends Command
{
    protected $signature = 'brochures:rebuild
        {--car= : Single car id to rebuild}
        {--missing : Only rebuild cars whose brochure_status is not "ready"}';

    protected $description = 'Generate cached CertiCheck brochure PDFs for cars';

    public function handle(BrochureGenerationService $svc): int
    {
        $query = Car::query()->where('has_certicheck', true);

        if ($carId = $this->option('car')) {
            $query->where('id', $carId);
        } elseif ($this->option('missing')) {
            $query->where(function ($q) {
                $q->where('brochure_status', '!=', 'ready')
                  ->orWhereNull('brochure_path');
            });
        }

        $cars = $query->orderBy('id')->get();

        if ($cars->isEmpty()) {
            $this->info('No cars match. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Rebuilding brochures for %d car(s).', $cars->count()));
        $bar = $this->output->createProgressBar($cars->count());

        $ok = $fail = 0;
        foreach ($cars as $car) {
            try {
                $svc->generate($car);
                $ok++;
            } catch (\Throwable $e) {
                $fail++;
                $this->newLine();
                $this->error(sprintf('car_id=%d failed: %s', $car->id, $e->getMessage()));
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);
        $this->info(sprintf('Done. ok=%d failed=%d', $ok, $fail));

        return $fail === 0 ? self::SUCCESS : self::FAILURE;
    }
}
