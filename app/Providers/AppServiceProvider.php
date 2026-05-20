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
        // Share the authenticated user instance with all views to
        // avoid "Undefined variable $user" errors in blade templates.
        View::composer('*', function ($view) {
            $view->with('user', auth()->user());
        });
        // Use Bootstrap 5 for pagination rendering
        Paginator::useBootstrapFive();
    }
}
