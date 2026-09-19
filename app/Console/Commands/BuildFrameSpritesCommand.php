<?php

namespace App\Console\Commands;

use App\Jobs\BuildFrameSpritesJob;
use App\Models\Car;
use Illuminate\Console\Command;

class BuildFrameSpritesCommand extends Command
{
    protected $signature = 'frames:sprites {car? : ID auta (domyślnie wszystkie)} {--force : przebuduj także auta, które mają już arkusze}';

    protected $description = 'Skleja klatki 360° w arkusze (szybkie ładowanie widoku 360 na stronie auta)';

    public function handle(): int
    {
        $q = Car::query()->where(function ($w) {
            $w->where('interior_frames_status', 'ready')->orWhere('exterior_frames_status', 'ready');
        });
        if ($id = $this->argument('car')) $q->whereKey($id);

        $n = 0;
        foreach ($q->get() as $car) {
            foreach (['interior', 'exterior'] as $side) {
                $has = $side === 'interior' ? $car->hasInteriorFrames() : $car->hasExteriorFrames();
                if (!$has) continue;
                if ($this->option('force')) $car->forceFill([$side . '_frames_meta' => null])->save();
                if (!empty($car->{$side . '_frames_meta'})) continue;
                BuildFrameSpritesJob::dispatchSync($car->id, $side);
                $car->refresh();
                $ok = !empty($car->{$side . '_frames_meta'});
                $this->line(sprintf('auto #%d %s: %s', $car->id, $side, $ok ? 'OK' : 'BŁĄD (patrz Rejestr błędów)'));
                $n++;
            }
        }
        $this->info("Gotowe, przetworzono: {$n}.");
        return self::SUCCESS;
    }
}
