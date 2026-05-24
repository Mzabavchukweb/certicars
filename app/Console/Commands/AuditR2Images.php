<?php

namespace App\Console\Commands;

use App\Models\CarImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AuditR2Images extends Command
{
    protected $signature = 'images:audit-r2 {--prune : Delete DB records whose R2 file is confirmed missing (no-op without this flag)}';
    protected $description = 'Audit CarImage paths against R2; optionally remove orphaned DB records';

    public function handle(): int
    {
        $driver = config('filesystems.disks.public.driver', 'local');

        if ($driver !== 's3') {
            $this->warn("Disk driver is '$driver'. R2 audit only runs when FILESYSTEM_DISK=s3.");
            return self::FAILURE;
        }

        // Use throw-enabled disk so we can distinguish "missing" from "auth error"
        $cfg          = config('filesystems.disks.public');
        $cfg['throw'] = true;
        $disk         = Storage::build($cfg);

        $images  = CarImage::with('car:id,title,slug')->orderBy('car_id')->get();
        $missing = [];
        $present = [];
        $skipped = [];

        $this->line('Checking ' . $images->count() . ' CarImage records…');
        $this->line('');

        foreach ($images as $img) {
            if (!$img->path || str_starts_with($img->path, 'http')) {
                $skipped[] = $img;
                continue;
            }

            try {
                if ($disk->fileExists($img->path)) {
                    $present[] = $img;
                } else {
                    $missing[] = $img;
                }
            } catch (\Throwable $e) {
                $this->error("  id={$img->id} path={$img->path} — CHECK FAILED: " . $e->getMessage());
                $skipped[] = $img;
            }
        }

        $this->info('Present in R2 : ' . count($present));
        $this->warn('Missing in R2  : ' . count($missing));
        $this->line('Skipped (http/empty): ' . count($skipped));
        $this->line('');

        if ($missing) {
            $this->line('<fg=red>MISSING (orphaned DB records):</>');
            foreach ($missing as $img) {
                $slug = $img->car?->slug ?? '?';
                $this->line("  id={$img->id}  car_id={$img->car_id} ({$slug})  type={$img->type}  path={$img->path}");
            }
        }

        if ($present) {
            $this->line('');
            $this->line('<fg=green>PRESENT in R2:</>');
            foreach ($present as $img) {
                $slug = $img->car?->slug ?? '?';
                $this->line("  id={$img->id}  car_id={$img->car_id} ({$slug})  type={$img->type}  path={$img->path}");
            }
        }

        if ($this->option('prune') && $missing) {
            $this->line('');
            $ids = array_map(fn($i) => $i->id, $missing);
            $this->warn('--prune: deleting ' . count($ids) . ' orphaned DB records (ids: ' . implode(', ', $ids) . ')');
            CarImage::whereIn('id', $ids)->delete();
            $this->info('Done. No files were deleted from R2 (they did not exist).');
        } elseif ($missing) {
            $this->line('');
            $this->line('Run with --prune to remove these ' . count($missing) . ' orphaned DB records.');
        }

        return count($missing) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
