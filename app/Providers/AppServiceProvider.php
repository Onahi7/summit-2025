<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load local test routes only in local environment
        if ($this->app->environment('local')) {
            $this->loadLocalTestRoutes();
        }
    }

    /**
     * Load local test routes for development environment
     */
    protected function loadLocalTestRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/local.php'));
    }
}
