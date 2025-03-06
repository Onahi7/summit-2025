<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Supabase connection
    |
    */

    'url' => env('SUPABASE_URL'),
    'key' => env('SUPABASE_KEY'),
    'secret_key' => env('SUPABASE_SECRET_KEY'),

    'options' => [
        'schema' => 'public',
        'headers' => [
            'X-Client-Info' => 'napps-conference',
        ],
    ],
];
