<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Loxo API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Loxo API settings. These values are used
    | to authenticate and communicate with the Loxo API.
    |
    */

    'domain' => env('LOXO_DOMAIN', 'app.loxo.co'),

    'agency_slug' => env('LOXO_AGENCY_SLUG'),

    'api_key' => env('LOXO_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */

    'timeout' => env('LOXO_TIMEOUT', 30),

    'retry_attempts' => env('LOXO_RETRY_ATTEMPTS', 3),

    'retry_delay' => env('LOXO_RETRY_DELAY', 1000), // milliseconds

    /*
    |--------------------------------------------------------------------------
    | Base API URL
    |--------------------------------------------------------------------------
    */

    'base_url' => env('LOXO_BASE_URL', 'https://{domain}/api/{agency_slug}'),
];
