<?php

namespace App\Http\Controllers;

use App\Models\UserMlProduct;
use App\Services\SoGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MLproductsController extends Controller
{
    private function sortProducts($collection)
    {
        return $collection
            ->sortBy(function ($p) {
                $n = strtolower($p->name ?? '');
                $priority = 3;

                // 1. Weekly Pass
                if (str_contains($n, 'weekly') || str_contains($n, 'အပတ်စဥ်')) {
                    $priority = 0;
                }
                // 2. New Products Pass / Twilight Pass / Monthly / Epic / Super Value
                elseif (str_contains($n, 'twilight') || str_contains($n, 'new products') || str_contains($n, 'monthly') || str_contains($n, 'လစဥ်') || str_contains($n, 'super value') || str_contains($n, 'epic')) {
                    $priority = 1;
                }
                // 3. Double FRC
                elseif (str_contains($n, 'frc') || str_contains($n, 'double')) {
                    $priority = 2;
                }

                return $priority * 1000000000 + (int) ($p->price ?? 0);
            })
            ->values();
    }

    private function extractPrice(array $item, string $region): int
    {
        $candidatesByRegion = [
            'myanmar' => ['price_mmk', 'mmk', 'mmk_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            'malaysia' => ['price_myr', 'myr', 'myr_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            'philippines' => ['price_php', 'php', 'php_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            'singapore' => ['price_sgd', 'sgd', 'sgd_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            'indonesia' => ['price_idr', 'idr', 'idr_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            'russia' => ['price_rub', 'rub', 'rub_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
        ];

        $keys = $candidatesByRegion[$region] ?? ['price', 'amount'];

        if (isset($item['prices']) && is_array($item['prices'])) {
            $map = $item['prices'];
            $regionKeyMap = [
                'myanmar' => ['mmk', 'MMK'],
                'malaysia' => ['myr', 'MYR'],
                'philippines' => ['php', 'PHP'],
                'singapore' => ['sgd', 'SGD'],
                'indonesia' => ['idr', 'IDR'],
                'russia' => ['rub', 'RUB'],
            ];
            foreach ($regionKeyMap[$region] ?? [] as $rk) {
                if (isset($map[$rk])) {
                    return (int) preg_replace('/[^\d]/', '', (string) $map[$rk]);
                }
            }
        }

        foreach ($keys as $k) {
            if (isset($item[$k])) {
                return (int) preg_replace('/[^\d]/', '', (string) $item[$k]);
            }
        }

        return 0;
    }

    private function extractDiamonds(array $item): int
    {
        if (isset($item['diamonds']) && is_numeric($item['diamonds'])) {
            return (int) $item['diamonds'];
        }
        if (isset($item['name'])) {
            if (preg_match('/(\d+)\s*💎/u', (string) $item['name'], $m)) {
                return (int) $m[1];
            }
            if (preg_match('/(\d+)\s*(diamond|diamonds)/i', (string) $item['name'], $m)) {
                return (int) $m[1];
            }
        }

        return 0;
    }

    public function checkId(Request $request)
    {
        $request->validate([
            'game_id' => 'required',
            'server_id' => 'required',
        ]);

        $service = new SoGameService;
        $result = $service->checkRole($request->game_id, $request->server_id);

        if ($result) {
            return response()->json($result);
        }

        return response()->json(['status' => false, 'message' => 'Invalid ID or Server']);
    }

    public function index(Request $request)
    {
        $region = $request->get('region', 'myanmar');
        $cacheKey = 'mlbb.products.'.$region;
        $mlproducts = Cache::remember($cacheKey, 30, function () use ($region) {
            $items = UserMlProduct::query()
                ->where('region', $region)
                ->where('status', 1)
                ->get()
                ->unique('product_id');

            return $this->sortProducts($items);
        });

        return view('mobilelegend', compact('mlproducts', 'region'));
    }
}
