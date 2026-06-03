<?php

namespace App\Jobs;

use App\Models\Car;
use App\Services\InteriorFrameExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExtractExteriorFramesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function __construct(public int $carId, public string $videoPath, public string $framesDir) {}

    public function handle(InteriorFrameExtractor $extractor): void
    {
        $car = Car::find($this->carId);
        if (!$car) {
            Log::warning('exterior.job.car_missing', ['car_id' => $this->carId]);
            return;
        }

        $car->forceFill([
            'exterior_frames_status' => 'processing',
            'exterior_frames_error'  => null,
        ])->save();

        try {
            $count = $extractor->extract($this->videoPath, $this->framesDir);

            $car->forceFill([
                'exterior_frames_status' => 'ready',
                'exterior_frames_count'  => $count,
                'exterior_frames_dir'    => $this->framesDir,
                'exterior_frames_error'  => null,
            ])->save();

            Log::info('exterior.job.ready', ['car_id' => $this->carId, 'count' => $count]);
        } catch (\Throwable $e) {
            $car->forceFill([
                'exterior_frames_status' => 'failed',
                'exterior_frames_error'  => mb_substr($e->getMessage(), 0, 500),
            ])->save();
            Log::error('exterior.job.failed', [
                'car_id' => $this->carId,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($car = Car::find($this->carId)) {
            $car->forceFill([
                'exterior_frames_status' => 'failed',
                'exterior_frames_error'  => mb_substr($e->getMessage(), 0, 500),
            ])->save();
        }
    }
}
