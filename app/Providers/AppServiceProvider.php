<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\FuzzyAhpService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FuzzyAhpService::class, function ($app) {
            return new FuzzyAhpService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap for pagination (optional)
        Paginator::useBootstrapFive();
        
        // Or use default Laravel pagination
        // Paginator::defaultView('pagination::tailwind');
        // Paginator::defaultSimpleView('pagination::simple-tailwind');
    }
}