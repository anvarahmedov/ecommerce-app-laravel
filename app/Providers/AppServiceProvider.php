<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;

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
        Vite::prefetch(concurrency: 3);

        Filament::serving(function () {
            // Add a custom button to the user menu
            Filament::registerUserMenuItems([
                'custom-action' => UserMenuItem::make()
                    ->label('Home') // The label of your button
                    ->url('http://127.0.0.1:8000') // The URL it should point to
                    ->icon('heroicon-o-home'), // Icon from Heroicons
            ]);
        });
    }
}
