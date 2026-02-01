<?php

namespace App\Http\Controllers;

use App\Models\UserMcggProduct;
use App\Services\McggGameService;

class McggController extends BaseGameController
{
    protected $modelClass = UserMcggProduct::class;
    protected $viewName = 'mcgg';
    protected $cacheKey = 'mcgg';
    protected $serviceClass = McggGameService::class;

    protected function getValidationRules()
    {
        return [
            'game_id' => 'required',
            'server_id' => 'required',
        ];
    }

    protected function sortProducts($collection)
    {
        return $collection
            ->sortBy(function ($p) {
                $n = strtolower($p->name ?? '');
                $priority = 3;

                // 1. Passes & Special
                if (str_contains($n, 'pass') || str_contains($n, 'weekly') || str_contains($n, 'monthly') || str_contains($n, 'fund')) {
                    $priority = 0;
                }
                // 2. Diamonds/Coins (Standard)
                elseif (str_contains($n, 'diamond') || str_contains($n, 'coin')) {
                    $priority = 2;
                }

                return $priority * 1000000000 + (int) ($p->price ?? 0);
            })
            ->values();
    }
}
