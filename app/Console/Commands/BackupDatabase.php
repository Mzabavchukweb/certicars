<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep=14 : How many recent backups to retain}';
    protected $description = 'Snapshot the SQLite database file into storage/backups and prune old copies';

    public function handle(): int
    {
        $dbPath = config('database.connections.sqlite.database');

        if (! is_string($dbPath) || ! file_exists($dbPath)) {
            $this->error("Database file not found at: {$dbPath}");
            return self::FAILURE;
        }

        $disk = Storage::disk('local');
        $disk->makeDirectory('backups');

        $name = 'backups/db-' . now()->format('Y-m-d_His') . '.sqlite';
        $bytes = $disk->put($name, file_get_contents($dbPath));

        if ($bytes === false) {
            $this->error('Failed to write backup file.');
            return self::FAILURE;
        }

        $this->info("Backup saved: {$name} (" . filesize($dbPath) . ' bytes source)');

        $keep = max(1, (int) $this->option('keep'));
        $files = collect($disk->files('backups'))
            ->filter(fn ($p) => str_ends_with($p, '.sqlite'))
            ->sortDesc()
            ->values();

        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep);
            foreach ($toDelete as $f) {
                $disk->delete($f);
                $this->line("Pruned: {$f}");
            }
        }

        return self::SUCCESS;
    }
}
