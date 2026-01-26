<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMlProduct;
use App\Models\UserPubgProduct;
use App\Models\UserMcggProduct;
use App\Models\UserWwmProduct;
use App\Models\PaymentMethod;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'game_type' => 'required|string|in:mlbb,pubg,mcgg,wwm',
            'product_id' => 'required|string',
            'player_id' => 'required|string',
            'server_id' => 'nullable|string',
            'region' => 'nullable|string',
            'zone_id' => 'nullable|string',
            'product_name' => 'nullable|string',
        ]);

        if (empty($data['server_id']) && ! empty($data['zone_id'])) {
            $data['server_id'] = $data['zone_id'];
        }

        $productName = $data['product_name'] ?? '';
        $amount = 0;
        if ($productName === '') {
            if ($data['game_type'] === 'mlbb') {
                $query = UserMlProduct::where('product_id', $data['product_id']);
                if (! empty($data['region'])) {
                    $query->where('region', $data['region']);
                }
                $product = $query->first();
                $productName = optional($product)->name ?: '';
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'pubg') {
                $product = UserPubgProduct::where('product_id', $data['product_id'])->first();
                $productName = optional($product)->name ?: '';
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'mcgg') {
                $product = UserMcggProduct::where('product_id', $data['product_id'])->first();
                $productName = optional($product)->name ?: '';
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'wwm') {
                $product = UserWwmProduct::where('product_id', $data['product_id'])->first();
                $productName = optional($product)->name ?: '';
                $amount = (int) ($product->price ?? 0);
            }
        } else {
            if ($data['game_type'] === 'mlbb') {
                $query = UserMlProduct::where('product_id', $data['product_id']);
                if (! empty($data['region'])) {
                    $query->where('region', $data['region']);
                }
                $product = $query->first();
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'pubg') {
                $product = UserPubgProduct::where('product_id', $data['product_id'])->first();
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'mcgg') {
                $product = UserMcggProduct::where('product_id', $data['product_id'])->first();
                $amount = (int) ($product->price ?? 0);
            } elseif ($data['game_type'] === 'wwm') {
                $product = UserWwmProduct::where('product_id', $data['product_id'])->first();
                $amount = (int) ($product->price ?? 0);
            }
        }

        $order = [
            'game_type' => $data['game_type'],
            'product_id' => $data['product_id'],
            'product_name' => $productName,
            'player_id' => $data['player_id'],
            'server_id' => $data['server_id'] ?? '',
            'region' => $data['region'] ?? '',
            'amount' => $amount > 0 ? $amount : 0,
        ];

        $paymentMethods = PaymentMethod::all();

        return view('payment', compact('order', 'paymentMethods'));
    }
}
