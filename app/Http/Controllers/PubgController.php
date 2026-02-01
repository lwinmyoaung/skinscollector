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
        // PUBG usually requires different parameters or service calls
        // For now, using default implementation but with strict validation
        $request->validate($this->getValidationRules());
        
        $service = new $this->serviceClass;
        $result = $service->checkId($request->game_id);

        if (($result['ok'] ?? false) || ($result['success'] ?? false)) {
            return response()->json([
                'result' => 1,
                'nickname' => $result['nickname'] ?? 'Unknown',
            ]);
        }

        return response()->json(['result' => 0, 'message' => 'Player not found']);
    }

    protected function sortProducts($collection)
    {
        return $collection
            ->sortBy(function ($p) {
                $n = strtolower($p->name ?? '');
                $priority = 3;

                // 1. High Priority: Royale Pass, Elite Pass, Prime
                if (str_contains($n, 'royale pass') || str_contains($n, 'elite pass') || str_contains($n, 'prime')) {
                    $priority = 0;
                }
                // 2. Medium Priority: Weekly, Monthly, Packs, Plus
                elseif (str_contains($n, 'weekly') || str_contains($n, 'monthly') || str_contains($n, 'pack') || str_contains($n, 'plus')) {
                    $priority = 1;
                }
                // 3. UC (Standard)
                elseif (str_contains($n, 'uc')) {
                    $priority = 2;
                }

                return $priority * 1000000000 + (int) ($p->price ?? 0);
            })
            ->values();
    }
}
