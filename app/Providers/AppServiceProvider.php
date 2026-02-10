<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Use custom pagination view without SVG icons
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Explicit route model binding for auto_greeting
        Route::bind('auto_greeting', function ($value) {
            return \App\Models\AutoGreetingRule::findOrFail($value);
        });

        // Register sidebar view composer
        View::composer('components.sidebar', \App\Http\ViewComposers\SidebarComposer::class);
    }
}
