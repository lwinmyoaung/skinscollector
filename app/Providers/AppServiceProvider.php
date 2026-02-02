<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\GameImage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

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
            $gameImages = Cache::remember('global.game_images', 3600, function () {
                return GameImage::all()->keyBy('game_code');
            });
            $view->with('gameImages', $gameImages);
        });
    }
}
