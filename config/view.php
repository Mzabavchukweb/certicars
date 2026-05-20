<?php

return [
    'paths' => [
        resource_path('views'),
    ],
    // Store compiled views in tmpfs (not NAS) so PHP loads them from RAM,
    // not a network-attached volume which has ~100ms per file read.
    'compiled' => env('VIEW_COMPILED_PATH', '/tmp/laravel-views'),
];
