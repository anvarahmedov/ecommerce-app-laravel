<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use App\Services\CartService;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, function() {
            return new CartService();
        });
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
                    ->url('http://localhost:8000') // The URL it should point to
                    ->icon('heroicon-o-home'), // Icon from Heroicons
            ]);


        });

    //    Inertia::share([
   //         'auth' => function () {
    //            return [
     //               'user' => auth()->user(),
     //           ];
      //      },
      //  ]);
      //  Filament::serving(function () {
      //      Filament::registerTheme(
      //          config('colors.primary') ,
      //      );
      //  });
    }
}
