<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\AdminMcggProduct;
use App\Models\AdminMlProduct;
use App\Models\AdminPubgProduct;
use App\Models\AdminWwmProduct;
use App\Models\Notification;
use App\Models\UserMcggProduct;
use App\Models\UserMlProduct;
use App\Models\UserPubgProduct;
use App\Models\UserWwmProduct;
use App\Services\LaravelPubgService;
use App\Services\McggGameService;
use App\Services\WwmGameService;
use App\Services\SoGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function submit(Request $request)
    {
        // Check Login
        if (! Auth::check()) {
            return redirect()->back()->with('show_login_modal', true);
        }

        // Detect Game Type
        $gameType = $request->input('game_type', 'mlbb'); // Default to mlbb if not set

        if ($gameType === 'pubg') {
            return $this->handlePubgOrder($request);
        }

        if ($gameType === 'mcgg') {
            return $this->handleMcggOrder($request);
        }

        if ($gameType === 'wwm') {
            return $this->handleWwmOrder($request);
        }

        // --- MLBB LOGIC (Existing) ---
        $request->validate([
            'player_id' => 'required',
            'zone_id' => 'required',
            'product_id' => 'required',
            'region' => 'required|in:myanmar,malaysia,philippines,singapore,indonesia,russia',
        ]);

        // Check Balance
        $selectedRegion = $request->input('region');
        $product = UserMlProduct::where('product_id', $request->input('product_id'))
            ->where('region', $selectedRegion)
            ->first();
        if (! $product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $service = new SoGameService;
        $check = $service->checkRole(
            $request->input('player_id'),
            $request->input('zone_id')
        );
        if (! $check || ! isset($check['region'])) {
            return redirect()->back()->with('error', 'Game account not found or invalid. Please check your ID and Zone ID.');
        }
        $userRegion = strtolower($check['region']);
        $normalize = function ($s) {
            $x = strtolower((string) $s);
            if (str_contains($x, 'myan')) {
                return 'myanmar';
            }
            if ($x === 'mm' || $x === 'mmk') {
                return 'myanmar';
            }
            if (str_contains($x, 'malay')) {
                return 'malaysia';
            }
            if ($x === 'my') {
                return 'malaysia';
            }
            if (str_contains($x, 'phil')) {
                return 'philippines';
            }
            if ($x === 'ph') {
                return 'philippines';
            }
            if (str_contains($x, 'sing')) {
                return 'singapore';
            }
            if ($x === 'sg') {
                return 'singapore';
            }
            if (str_contains($x, 'indo')) {
                return 'indonesia';
            }
            if ($x === 'id') {
                return 'indonesia';
            }
            if (str_contains($x, 'rus')) {
                return 'russia';
            }
            if ($x === 'ru') {
                return 'russia';
            }

            return $x;
        };
        $userRegionNorm = $normalize($userRegion);
        $productRegionNorm = $normalize($product->region);
        if ($userRegionNorm !== $productRegionNorm) {
            return redirect()->back()->with('error', 'Please buy products from the same region as your game account.');
        }

        $user = Auth::user();
        $effectivePrice = (int) $product->price;

        $result = $service->buyProduct(
            $request->input('player_id'),
            $request->input('zone_id'),
            $request->input('product_id')
        );

        if ($result['success']) {
            $gameAccountName = $check['username'] ?? $request->input('player_id');

            // Record Order
            $this->recordOrder(
                $user,
                'mlbb',
                $product,
                $request->input('player_id'),
                $request->input('zone_id')
            );

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Purchase Successful',
                'message' => "Game Account: {$gameAccountName} | Product: {$product->name} | Cost: ".number_format($effectivePrice).' Ks.',
                'type' => 'success',
            ]);

            return redirect()->back()->with('success', 'Order submitted successfully! '.$result['message']);
        } else {
            return redirect()->back()->with('error', 'Order failed: '.$result['message']);
        }
    }

    private function handlePubgOrder(Request $request)
    {
        $request->validate([
            'player_id' => 'required',
            'product_id' => 'required',
        ]);

        $product = UserPubgProduct::where('product_id', $request->input('product_id'))
            ->where('status', 1)
            ->first();

        if (! $product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = Auth::user();
        $effectivePrice = (int) $product->price;

        // Verify ID again before purchase (Optional but safer)
        $service = new LaravelPubgService;
        $check = $service->checkId($request->input('player_id'));

        // PUBG Service checkId returns result=1 on success
        if (! isset($check['result']) || $check['result'] !== 1) {
            return redirect()->back()->with('error', 'Invalid Game ID.');
        }

        $gameAccountName = $check['nickname'] ?? $check['username'] ?? 'Unknown';

        // Execute Order
        $result = $service->order(
            $request->input('player_id'),
            $request->input('product_id')
        );

        if ($result['success']) {
            // Record Order
            $this->recordOrder(
                $user,
                'pubg',
                $product,
                $request->input('player_id'),
                $request->input('server_id')
            );

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Purchase Successful',
                'message' => "PUBG Mobile | Account: {$gameAccountName} ({$request->player_id}) | Product: {$product->name} | Cost: ".number_format($effectivePrice).' Ks.',
                'type' => 'success',
            ]);

            return redirect()->back()->with('success', 'Order submitted successfully! ');
        } else {
            return redirect()->back()->with('error', 'Order failed: '.($result['message'] ?? 'Unknown error'));
        }
    }

    private function handleMcggOrder(Request $request)
    {
        $request->validate([
            'player_id' => 'required',
            'server_id' => 'required',
            'product_id' => 'required',
        ]);

        $product = UserMcggProduct::where('product_id', $request->input('product_id'))
            ->where('status', 1)
            ->first();

        if (! $product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = Auth::user();
        $effectivePrice = (int) $product->price;

        // 1. Verify ID first (as requested)
        $service = new McggGameService;
        $check = $service->checkId($request->input('player_id'), $request->input('server_id'));

        if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
            $errorDetail = $check['error'] ?? $check['message'] ?? 'Unknown error';
            return redirect()->back()->with('error', "Player not found: {$errorDetail}");
        }

        // Attempt to extract nickname
        $data = $check['data'] ?? [];
        $gameAccountName = $data['nickname'] ?? $data['username'] ?? $data['name'] ?? 'Unknown';

        // 2. Execute Order
        // Note: passing product diamonds as amount might be needed, or null
        $amount = $product->diamonds;

        $result = $service->order(
            $request->input('player_id'),
            $request->input('server_id'),
            $request->input('product_id'),
            null, // price (optional)
            $amount // amount (optional)
        );

        if ($result['ok']) {
            // Record Order
            $this->recordOrder(
                $user,
                'mcgg',
                $product,
                $request->input('player_id'),
                $request->input('server_id')
            );

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Purchase Successful',
                'message' => "MCGG | Account: {$gameAccountName} ({$request->player_id}) | Product: {$product->name} | Cost: ".number_format($effectivePrice).' Ks.',
                'type' => 'success',
            ]);

            return redirect()->back()->with('success', 'Order submitted successfully!');
        } else {
            // Flash error to user as requested
            // Use 'error' from result or generic message
            $errorMsg = $result['error'] ?? 'Unknown error during order placement.';
            if (isset($result['status'])) {
                $errorMsg .= " (Status: {$result['status']})";
            }

            return redirect()->back()->with('error', 'Order failed: '.$errorMsg);
        }
    }

    private function handleWwmOrder(Request $request)
    {
        $request->validate([
            'player_id' => 'required',
            'product_id' => 'required',
        ]);

        $product = UserWwmProduct::where('product_id', $request->input('product_id'))
            ->where('status', 1)
            ->first();

        if (! $product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = Auth::user();
        $effectivePrice = (int) $product->price;

        // 1. Verify ID first (as requested)
        $service = new WwmGameService;
        $check = $service->checkId($request->input('player_id'), $request->input('server_id', ''));

        if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
            $errorDetail = $check['error'] ?? $check['message'] ?? 'Unknown error';
            return redirect()->back()->with('error', "Player not found: {$errorDetail}");
        }

        // Attempt to extract nickname
        $data = $check['data'] ?? [];
        $gameAccountName = $data['nickname'] ?? $data['username'] ?? $data['name'] ?? 'Unknown';

        // 2. Execute Order
        // Note: passing product diamonds as amount might be needed, or null
        $amount = $product->diamonds;

        $result = $service->order(
            $request->input('player_id'),
            $request->input('product_id'),
            $request->input('server_id', ''),
            null, // price (optional)
            $amount // amount (optional)
        );

        if ($result['ok']) {
            // Record Order
            $this->recordOrder(
                $user,
                'wwm',
                $product,
                $request->input('player_id'),
                $request->input('server_id', '')
            );

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Purchase Successful',
                'message' => "WWM | Account: {$gameAccountName} ({$request->player_id}) | Product: {$product->name} | Cost: ".number_format($effectivePrice).' Ks.',
                'type' => 'success',
            ]);

            return redirect()->back()->with('success', 'Order submitted successfully!');
        } else {
            // Flash error to user as requested
            // Use 'error' from result or generic message
            $errorMsg = $result['error'] ?? 'Unknown error during order placement.';
            if (isset($result['status'])) {
                $errorMsg .= " (Status: {$result['status']})";
            }

            return redirect()->back()->with('error', 'Order failed: '.$errorMsg);
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function recordOrder($user, $game, $product, $playerId, $serverId = null)
    {
        $costPrice = 0;
        
        // Find Cost Price
        if ($game === 'mlbb') {
             $adminProduct = AdminMlProduct::where('product_id', $product->product_id)
                ->where('region', $product->region)
                ->first();
             $costPrice = $adminProduct ? $adminProduct->price : 0;
        } elseif ($game === 'pubg') {
             $adminProduct = AdminPubgProduct::where('product_id', $product->product_id)->first();
             $costPrice = $adminProduct ? $adminProduct->price : 0;
        } elseif ($game === 'mcgg') {
             $adminProduct = AdminMcggProduct::where('product_id', $product->product_id)->first();
             $costPrice = $adminProduct ? $adminProduct->price : 0;
        } elseif ($game === 'wwm') {
             $adminProduct = AdminWwmProduct::where('product_id', $product->product_id)->first();
             $costPrice = $adminProduct ? $adminProduct->price : 0;
        }

        $sellingPrice = $product->price;
        $profit = $sellingPrice - $costPrice;

        Order::create([
            'user_id' => $user->id,
            'game' => $game,
            'product_id' => $product->product_id,
            'product_name' => $product->name,
            'player_id' => $playerId,
            'server_id' => $serverId,
            'selling_price' => $sellingPrice,
            'cost_price' => $costPrice,
            'profit' => $profit,
            'status' => 'success',
        ]);
    }
}
