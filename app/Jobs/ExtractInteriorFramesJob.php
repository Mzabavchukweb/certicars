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

class ExtractInteriorFramesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function __construct(public int $carId, public string $videoPath, public string $framesDir) {}

    public function handle(InteriorFrameExtractor $extractor): void
    {
        $car = Car::find($this->carId);
        if (!$car) {
            Log::warning('interior.job.car_missing', ['car_id' => $this->carId]);
            return;
        }

        $car->forceFill([
            'interior_frames_status' => 'processing',
            'interior_frames_error'  => null,
        ])->save();

        try {
            $count = $extractor->extract($this->videoPath, $this->framesDir);

            $car->forceFill([
                'interior_frames_status' => 'ready',
                'interior_frames_count'  => $count,
                'interior_frames_dir'    => $this->framesDir,
                'interior_frames_error'  => null,
            ])->save();

            Log::info('interior.job.ready', ['car_id' => $this->carId, 'count' => $count]);
        } catch (\Throwable $e) {
            $car->forceFill([
                'interior_frames_status' => 'failed',
                'interior_frames_error'  => mb_substr($e->getMessage(), 0, 500),
            ])->save();
            Log::error('interior.job.failed', [
                'car_id' => $this->carId,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($car = Car::find($this->carId)) {
            $car->forceFill([
                'interior_frames_status' => 'failed',
                'interior_frames_error'  => mb_substr($e->getMessage(), 0, 500),
            ])->save();
        }
    }
}
