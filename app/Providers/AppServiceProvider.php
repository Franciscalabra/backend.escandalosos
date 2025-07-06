<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Usar Bootstrap para paginación
        Paginator::useBootstrap();
        
        // Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}