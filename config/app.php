<?php

return [
    'name'  => env('APP_NAME', 'Laravel'),
    'env'   => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url'   => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale'   => 'en',
    'fallback_locale' => 'en',
    'faker_locale'    => 'en_US',
    'key'    => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],

    'random_user_api'     => env('RANDOM_USER_API', 'https://randomuser.me/api/'),
    'random_user_results' => (int) env('RANDOM_USER_RESULTS', 50),

    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    'aliases' => \Illuminate\Support\Facades\Facade::defaultAliases()->merge([
        'Ziggy' => \Tightenco\Ziggy\Ziggy::class,
    ])->toArray(),
];
