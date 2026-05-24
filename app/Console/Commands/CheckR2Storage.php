<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CheckR2Storage extends Command
{
    protected $signature = 'r2:check {--keep : Do not delete the test file after upload}';
    protected $description = 'Verify Cloudflare R2 storage: upload, public read, delete';

    public function handle(): int
    {
        $this->line('');
        $this->line('=== R2 / S3 Storage Check ===');
        $this->line('');

        // 1. Show config (mask credentials)
        $driver  = config('filesystems.disks.public.driver', 'unknown');
        $bucket  = config('filesystems.disks.public.bucket', '–');
        $endpoint = config('filesystems.disks.public.endpoint', '–');
        $region  = config('filesystems.disks.public.region', '–');
        $url     = config('filesystems.disks.public.url', '–');
        $key     = config('filesystems.disks.public.key', '');
        $secret  = config('filesystems.disks.public.secret', '');
        $path    = env('AWS_USE_PATH_STYLE_ENDPOINT', 'false');

        $this->line('<fg=cyan>Config:</>');
        $this->line("  FILESYSTEM_DISK          = " . env('FILESYSTEM_DISK', '(not set → local)'));
        $this->line("  disk.public.driver       = $driver");
        $this->line("  AWS_BUCKET               = $bucket");
        $this->line("  AWS_ENDPOINT             = $endpoint");
        $this->line("  AWS_DEFAULT_REGION       = $region");
        $this->line("  AWS_URL (public CDN URL) = $url");
        $this->line("  AWS_USE_PATH_STYLE       = $path");
        $this->line("  AWS_ACCESS_KEY_ID        = " . ($key ? substr($key, 0, 6) . '…' . substr($key, -4) : '<fg=red>MISSING</>'));
        $this->line("  AWS_SECRET_ACCESS_KEY    = " . ($secret ? '***set***' : '<fg=red>MISSING</>'));
        $this->line('');

        if ($driver !== 's3') {
            $this->warn("Disk driver is '$driver', not 's3'. Set FILESYSTEM_DISK=s3 in Railway to use R2.");
            $this->line('');
            $this->info('Local disk is active — no R2 test possible.');
            return self::SUCCESS;
        }

        // 2. Upload
        $testPath = '_r2_check/test-' . now()->format('Ymd-His') . '.txt';
        $testContent = 'certicars-r2-check ' . now()->toIso8601String();

        $this->line('<fg=cyan>1. Upload…</>');
        try {
            $ok = Storage::disk('public')->put($testPath, $testContent, 'public');
            if (!$ok) {
                $this->error("   FAIL — put() returned false. Check credentials and bucket permissions.");
                return self::FAILURE;
            }
            $this->info("   OK — uploaded to: $testPath");
        } catch (\Throwable $e) {
            $this->error("   FAIL — " . $e->getMessage());
            $this->line("   Check: AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY / AWS_BUCKET / AWS_ENDPOINT");
            $this->line("   R2 token needs: Object Read, Object Write on this bucket.");
            return self::FAILURE;
        }

        // 3. Get URL
        $this->line('<fg=cyan>2. Generate public URL…</>');
        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $publicUrl = $disk->url($testPath);
            $this->info("   URL: $publicUrl");
        } catch (\Throwable $e) {
            $this->error("   FAIL — " . $e->getMessage());
            $publicUrl = null;
        }

        // 4. Public HTTP read
        $this->line('<fg=cyan>3. HTTP GET public URL…</>');
        if ($publicUrl) {
            try {
                $response = Http::timeout(10)->get($publicUrl);
                $status   = $response->status();
                if ($response->successful() && str_contains($response->body(), 'certicars-r2-check')) {
                    $this->info("   OK — HTTP $status, body matches.");
                } elseif ($response->status() === 403) {
                    $this->warn("   HTTP 403 — Bucket public access is disabled.");
                    $this->line("   Fix: enable 'Allow Access' on the R2 bucket public domain in Cloudflare dashboard.");
                } elseif ($response->status() === 404) {
                    $this->warn("   HTTP 404 — File not found via public URL. Check AWS_URL matches bucket public domain.");
                } else {
                    $this->warn("   HTTP $status — unexpected. Body: " . substr($response->body(), 0, 200));
                }
            } catch (\Throwable $e) {
                $this->error("   FAIL — " . $e->getMessage());
            }
        } else {
            $this->warn("   SKIP — no URL generated.");
        }

        // 5. Delete
        if ($this->option('keep')) {
            $this->line('<fg=cyan>4. Delete…</>');
            $this->line("   SKIPPED (--keep flag). File left at: $testPath");
        } else {
            $this->line('<fg=cyan>4. Delete…</>');
            try {
                Storage::disk('public')->delete($testPath);
                $this->info("   OK — file deleted.");
            } catch (\Throwable $e) {
                $this->warn("   FAIL — " . $e->getMessage());
                $this->line("   R2 token needs: Object Delete permission.");
            }

            // Confirm deletion via HTTP
            if ($publicUrl) {
                try {
                    $response = Http::timeout(10)->get($publicUrl);
                    if ($response->status() === 404) {
                        $this->info("   Confirmed: HTTP 404 — file gone from public URL.");
                    } else {
                        $this->warn("   File still accessible after delete? HTTP " . $response->status());
                        $this->line("   (R2 CDN may cache briefly; this is usually not a bug.)");
                    }
                } catch (\Throwable $e) {
                    $this->line("   Could not confirm deletion via HTTP: " . $e->getMessage());
                }
            }
        }

        $this->line('');
        $this->line('=== Done ===');
        return self::SUCCESS;
    }
}
