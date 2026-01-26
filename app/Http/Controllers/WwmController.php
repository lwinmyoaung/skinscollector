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
}
