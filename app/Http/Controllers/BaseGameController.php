<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

abstract class BaseGameController extends Controller
{
    protected $modelClass;
    protected $viewName;
    protected $cacheKey;
    protected $serviceClass;

    public function index(Request $request)
    {
        $products = Cache::remember($this->cacheKey . '.products', 3600, function () {
            return $this->modelClass::query()
                ->where('status', 1)
                ->orderBy('price')
                ->get();
        });

        // Apply custom sorting if defined in child controller
        $products = $this->sortProducts($products);

        return view($this->viewName, compact('products'));
    }

    /**
     * Default sorting: no change (already sorted by price in query)
     * Override this in child controllers to apply custom sorting logic.
     */
    protected function sortProducts($products)
    {
        return $products;
    }

    /**
     * Common checkId method for games that follow the standard pattern.
     * Override this if the game requires specific logic (like PUBG).
     */
    public function checkId(Request $request)
    {
        $request->validate($this->getValidationRules());

        $service = new $this->serviceClass;
        $serverId = (string) $request->input('server_id', '');
        
        // Some services require server_id, some might be optional.
        // Assuming checkId signature is checkId($gameId, $serverId)
        $result = $service->checkId($request->game_id, $serverId);

        if (($result['ok'] ?? false) || ($result['success'] ?? false)) {
            return response()->json([
                'result' => 1,
                'nickname' => $result['nickname'] ?? 'Unknown',
            ]);
        }
        
        \Illuminate\Support\Facades\Log::warning(class_basename($this) . ' Check Failed', [
            'id' => $request->game_id,
            'server' => $serverId,
            'result' => $result
        ]);

        return response()->json(['result' => 0, 'message' => 'Player not found']);
    }

    protected function getValidationRules()
    {
        return [
            'game_id' => 'required',
        ];
    }
}
