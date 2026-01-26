<?php

namespace App\Http\Controllers;

use App\Models\UserPubgProduct;
use App\Services\LaravelPubgService;
use Illuminate\Http\Request;

class PubgController extends BaseGameController
{
    protected $modelClass = UserPubgProduct::class;
    protected $viewName = 'pubg';
    protected $cacheKey = 'pubg';
    protected $serviceClass = LaravelPubgService::class;

    public function checkId(Request $request)
    {
        $request->validate([
            'game_id' => 'required',
        ]);

        $service = new LaravelPubgService;
        $result = $service->checkId($request->game_id, $request->input('server_id', '1'));

        if (isset($result['result']) && $result['result'] === 1) {
            return response()->json($result);
        }

        return response()->json(['error' => 'Invalid ID'], 422);
    }
}
