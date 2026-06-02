<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
     | Google Maps Embed API — used by the public footer location preview.
     | Get a key at: https://console.cloud.google.com/google/maps-apis
     | enable: "Maps Embed API" (always free, no quota). Restrict the key
     | by HTTP referrer to the production domain. Lat/lng can be
     | overridden per-environment for staging.
     */
    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'lat'     => env('GOOGLE_MAPS_LAT', '53.3502947'),
        'lng'     => env('GOOGLE_MAPS_LNG', '14.9399374'),
        'zoom'    => (int) env('GOOGLE_MAPS_ZOOM', 17),
        'label'   => env('GOOGLE_MAPS_LABEL', 'Lipnik'),
    ],

];
