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
}
