<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
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
        Filament::serving(function () {
            foreach (['admin', 'teacher', 'student'] as $guard) {
                if (Auth::guard($guard)->check()) {
                    Auth::shouldUse($guard);
                    break;
                }
            }
        });
    }
}
