<?php

namespace App\Http\Controllers;

use App\Models\UserWwmProduct;
use App\Services\WwmGameService;

class WwmController extends BaseGameController
{
    protected $modelClass = UserWwmProduct::class;
    protected $viewName = 'wwm';
    protected $cacheKey = 'wwm';
    protected $serviceClass = WwmGameService::class;

    protected function sortProducts($collection)
    {
        return $collection
            ->sortBy(function ($p) {
                $n = strtolower($p->name ?? '');
                $priority = 3;

                // 1. Passes & Special
                if (str_contains($n, 'pass') || str_contains($n, 'weekly') || str_contains($n, 'monthly') || str_contains($n, 'fund') || str_contains($n, 'pack')) {
                    $priority = 0;
                }
                // 2. Currency (Standard)
                elseif (str_contains($n, 'jade') || str_contains($n, 'ingot') || str_contains($n, 'gold')) {
                    $priority = 2;
                }

                return $priority * 1000000000 + (int) ($p->price ?? 0);
            })
            ->values();
    }
}
