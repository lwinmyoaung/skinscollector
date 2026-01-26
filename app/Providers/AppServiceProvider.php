<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\GameImage;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! class_exists('NotificationController')) {
            class_alias(\App\Http\Controllers\NotificationController::class, 'NotificationController');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        View::composer(['games', 'mobilelegend', 'pubg', 'mcgg', 'wwm'], function ($view) {
            $view->with('gameImages', GameImage::all()->keyBy('game_code'));
        });
    }
}
