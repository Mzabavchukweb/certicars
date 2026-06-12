<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily SQLite snapshot kept on the persistent volume.
Schedule::command('backup:database')
    ->dailyAt('03:30')
    ->withoutOverlapping()
    ->onOneServer();

// Reap brochure rows stuck in `generating` for >10 minutes — the queue
// worker died mid-render (Chromium crash, OOM, container restart) without
// flipping status to `failed`. Without this sweep customers see the
// "preparing" modal indefinitely.
Schedule::command('brochures:reap-stuck')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
