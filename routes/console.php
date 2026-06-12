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

// Synchronous safety-net rebuild every 10 minutes. The primary path is the
// queue worker pickup of `RegenerateBrochureJob` (PR #129), but if the
// worker is somehow not running (Railway container restart loop, supervisord
// misconfig, queue:work silent crash), brochures never become `ready` and
// customers see the "preparing" modal indefinitely. This sweep finds any
// CertiCheck car that is NOT in `ready` state (missing, generating, failed)
// and re-runs generation synchronously. It's the same code the queue worker
// would execute — just guaranteed to run by the scheduler.
//
// `--missing` covers all non-ready states (see RebuildBrochuresCommand). With
// `withoutOverlapping(20)` two scheduler ticks won't dogpile on a slow
// Chromium render. Each car render is 4-15s so a backlog of 10 missing
// brochures takes ~2 min to clear — still safely inside the 10-minute
// interval.
Schedule::command('brochures:rebuild --missing')
    ->everyTenMinutes()
    ->withoutOverlapping(20)
    ->onOneServer()
    ->runInBackground();
