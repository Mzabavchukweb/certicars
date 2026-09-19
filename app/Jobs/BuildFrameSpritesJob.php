<?php

namespace App\Jobs;

use App\Models\Car;
use App\Models\ErrorLog;
use App\Services\FrameSpriteBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Arkusze 360° dla aut, których klatki powstały przed wprowadzeniem arkuszy.
 * Pobiera gotowe klatki z dysku (R2), skleja je i zapisuje meta.
 * Nowe filmy dostają arkusze od razu w InteriorFrameExtractor.
 */
class BuildFrameSpritesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(public int $carId, public string $side) {}

    public function handle(FrameSpriteBuilder $builder): void
    {
        if (!in_array($this->side, ['interior', 'exterior'], true)) return;
        $car = Car::find($this->carId);
        if (!$car) return;

        $has = $this->side === 'interior' ? $car->hasInteriorFrames() : $car->hasExteriorFrames();
        if (!$has || !empty($car->{$this->side . '_frames_meta'})) return;

        $dir   = $car->{$this->side . '_frames_dir'};
        $count = (int) $car->{$this->side . '_frames_count'};
        $disk  = Storage::disk('public');

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cc_sprites_' . bin2hex(random_bytes(6));
        @mkdir($tmp, 0700, true);
        try {
            for ($i = 1; $i <= $count; $i++) {
                $name = sprintf('frame_%03d.jpg', $i);
                $data = $disk->get($dir . '/' . $name);
                if ($data === null) {
                    throw new \RuntimeException("Brak klatki {$name}.");
                }
                file_put_contents($tmp . DIRECTORY_SEPARATOR . $name, $data);
            }
            $ffmpeg = config('services.ffmpeg.bin') ?: 'ffmpeg';
            $meta = $builder->build($ffmpeg, $tmp, $count, $dir);

            $car->refresh();
            // film mógł zostać podmieniony w trakcie — wtedy nie nadpisujemy
            if ($car->{$this->side . '_frames_dir'} === $dir && (int) $car->{$this->side . '_frames_count'} === $count) {
                $car->forceFill([$this->side . '_frames_meta' => $meta])->save();
            }
        } catch (\Throwable $e) {
            ErrorLog::record('frames.sprites', "Arkusze 360 ({$this->side}) nie powstały: " . $e->getMessage(), [], 'warning', $this->carId);
        } finally {
            foreach (glob($tmp . DIRECTORY_SEPARATOR . '*') ?: [] as $f) @unlink($f);
            @rmdir($tmp);
        }
    }
}
