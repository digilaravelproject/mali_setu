<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            if (!array_key_exists('user', $view->getData())) {
                $view->with('user', auth()->user());
            }
        });
        // Use Bootstrap 5 for pagination rendering
        Paginator::useBootstrapFive();
    }
}
