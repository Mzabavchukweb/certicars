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
