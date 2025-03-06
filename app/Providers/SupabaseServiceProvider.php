<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class SupabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('supabase', function ($app) {
            return new Client([
                'base_uri' => config('supabase.url'),
                'headers' => [
                    'apikey' => config('supabase.key'),
                    'Authorization' => 'Bearer ' . config('supabase.key'),
                ]
            ]);
        });
    }

    public function boot()
    {
        //
    }
}
