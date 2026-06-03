<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Slices an admin-uploaded pan-around video into a fixed-count JPEG sequence
 * using ffmpeg. Used by both the interior viewer and the exterior walk-around
 * viewer — the catalog page drag-scrubs through the sequence client-side
 * (Copart-style) instead of streaming the original video.
 *
 * Why a fixed count: the frontend pre-loads the sequence and maps pointer
 * delta → frame index. A predictable frame count makes the scrubber feel
 * uniform regardless of source video duration (3 s phone clip vs 10 s
 * tripod pan both end up at FRAME_COUNT evenly-distributed frames).
 *
 * Name kept for backwards-compat with the wizard view that references the
 * constants; nothing about the implementation is interior-specific.
 */
class InteriorFrameExtractor
{
    public const FRAME_COUNT = 60;
    public const FRAME_WIDTH = 1280;
    public const JPEG_QUALITY = 4; // ffmpeg `q:v` (lower = better; 2-5 is the practical range)
    public const TIMEOUT_SECONDS = 600;

    /**
     * Decode `$videoPath` (relative path on the public disk), write 60 JPEGs
     * into `$framesDir` on the public disk, and return the count actually
     * written.
     *
     * Throws on any ffmpeg failure so the caller (the job) can mark the car
     * row as `failed` with the error message attached.
     */
    public function extract(string $videoPath, string $framesDir): int
    {
        $ffmpeg = $this->resolveFfmpegBinary();
        $disk   = Storage::disk('public');

        if (!$disk->exists($videoPath)) {
            throw new RuntimeException("Interior video missing: {$videoPath}");
        }

        // ffmpeg cannot read from a Laravel Storage stream directly when the
        // disk is S3/R2 — we always copy the source to a local temp file so
        // the same code path works on the Railway volume and on Cloudflare.
        $localVideo = tempnam(sys_get_temp_dir(), 'cc_interior_src_');
        $localFrameDir = $this->makeTempDir('cc_interior_frames_');

        try {
            $contents = $disk->get($videoPath);
            if ($contents === null) {
                throw new RuntimeException("Interior video unreadable: {$videoPath}");
            }
            file_put_contents($localVideo, $contents);

            $duration = $this->probeDurationSeconds($localVideo, $ffmpeg);
            if ($duration <= 0.1) {
                throw new RuntimeException('Interior video has no usable duration (probe returned 0).');
            }

            // Pick fps so that ~FRAME_COUNT frames come out regardless of how
            // long the source video is. The fps filter is rational; ffmpeg
            // rounds, so we may end up with FRAME_COUNT ±1.
            $fps = max(1, (int) round(self::FRAME_COUNT / $duration));

            $outputPattern = $localFrameDir . DIRECTORY_SEPARATOR . 'frame_%03d.jpg';

            $process = new Process([
                $ffmpeg,
                '-y',                                 // overwrite existing
                '-loglevel', 'error',
                '-i', $localVideo,
                '-vf', sprintf('fps=%d,scale=%d:-2', $fps, self::FRAME_WIDTH),
                '-q:v', (string) self::JPEG_QUALITY,
                '-frames:v', (string) (self::FRAME_COUNT + 4), // small overshoot, we'll trim
                $outputPattern,
            ]);
            $process->setTimeout(self::TIMEOUT_SECONDS);

            try {
                $process->run();
            } catch (ProcessTimedOutException $e) {
                throw new RuntimeException('ffmpeg timed out while extracting interior frames.');
            }

            if (!$process->isSuccessful()) {
                Log::error('interior.ffmpeg.failed', [
                    'video' => $videoPath,
                    'exit'  => $process->getExitCode(),
                    'err'   => substr($process->getErrorOutput(), 0, 2000),
                ]);
                throw new RuntimeException('ffmpeg returned a non-zero exit code while extracting frames.');
            }

            $localFrames = glob($localFrameDir . DIRECTORY_SEPARATOR . 'frame_*.jpg') ?: [];
            sort($localFrames, SORT_NATURAL);
            if (count($localFrames) === 0) {
                throw new RuntimeException('ffmpeg produced no frames (empty output directory).');
            }
            // Trim overshoot — never publish more than FRAME_COUNT.
            $localFrames = array_slice($localFrames, 0, self::FRAME_COUNT);

            // Wipe any prior frames in this car's directory so we never serve a
            // half-old + half-new sequence.
            $this->clearRemoteDir($disk, $framesDir);

            foreach ($localFrames as $index => $localPath) {
                $remoteName = sprintf('frame_%03d.jpg', $index + 1);
                $stream = fopen($localPath, 'rb');
                $disk->put($framesDir . '/' . $remoteName, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            return count($localFrames);
        } finally {
            @unlink($localVideo);
            $this->removeDir($localFrameDir);
        }
    }

    /**
     * Best-effort total duration probe via `ffmpeg -i` (we already require
     * the ffmpeg binary; pulling in ffprobe just for this is wasteful).
     * Returns 0 if the duration line cannot be parsed.
     */
    private function probeDurationSeconds(string $localVideo, string $ffmpeg): float
    {
        $probe = new Process([$ffmpeg, '-hide_banner', '-i', $localVideo]);
        $probe->setTimeout(30);
        $probe->run(); // ffmpeg exits with 1 when no output is requested — error output still contains the metadata.
        $err = $probe->getErrorOutput();
        if (preg_match('/Duration:\s*(\d+):(\d+):(\d+(?:\.\d+)?)/', $err, $m)) {
            return ((int) $m[1]) * 3600 + ((int) $m[2]) * 60 + (float) $m[3];
        }
        return 0.0;
    }

    private function resolveFfmpegBinary(): string
    {
        $configured = config('services.ffmpeg.bin');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }
        return 'ffmpeg';
    }

    private function makeTempDir(string $prefix): string
    {
        $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . bin2hex(random_bytes(6));
        if (!mkdir($base, 0700, true) && !is_dir($base)) {
            throw new RuntimeException("Could not create temp dir: {$base}");
        }
        return $base;
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($dir);
    }

    private function clearRemoteDir($disk, string $dir): void
    {
        try {
            foreach ($disk->files($dir) as $existing) {
                $disk->delete($existing);
            }
        } catch (\Throwable $e) {
            // Directory may not exist yet on first extraction — that is fine.
        }
    }
}
