<?php

namespace App\Http\Controllers;

use App\Models\AdminMcggProduct;
use App\Models\AdminMlProduct;
use App\Models\AdminPubgProduct;
use App\Models\AdminWwmProduct;
use App\Models\KpayOrder;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\UserMcggProduct;
use App\Models\UserMlProduct;
use App\Models\UserPubgProduct;
use App\Models\UserWwmProduct;
use App\Services\LaravelPubgService;
use App\Services\McggGameService;
use App\Services\SoGameService;
use App\Services\WwmGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentConfirmController extends Controller
{
    public function userIndex(Request $request)
    {
        $query = KpayOrder::where('user_id', Auth::id());

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->game_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        $games = ['mlbb', 'pubg', 'mcgg', 'wwm'];
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];

        return view('user.kpay_orders', compact('orders', 'games', 'statuses'));
    }

    public function fetchUserOrders()
    {
        $orders = KpayOrder::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'game_type' => strtoupper($order->game_type),
                    'product_name' => $order->product_name ?: $order->product_id,
                    'amount' => number_format($order->amount),
                    'status' => ucfirst($order->status),
                    'status_color' => match ($order->status) {
                        'approved', 'success' => 'success',
                        'cancelled', 'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary',
                    },
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'transaction_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,heic|max:5120',
            'kpay_phone' => 'required|string',
            'game_type' => 'required|string',
            'player_id' => 'required|string',
            'server_id' => 'nullable|string',
            'product_id' => 'required|string',
            'amount' => 'required|numeric',
            'product_name' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'region' => 'nullable|string',
            'zone_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Order validation failed', ['errors' => $validator->errors()->toArray(), 'input' => $request->except('transaction_image')]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->route('payment.retry')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('transaction_image')) {
            $file = $request->file('transaction_image');
            
            // Option 1: Fast Storage Flow
            // Upload to adminimages/topups
            $path = $file->store('topups', 'adminimages');
            
            // Return clean URL path (e.g. topups/filename.jpg) instead of storage path
            $data['transaction_image'] = $path;
        }

        $zoneId = $request->input('zone_id');
        if (! ($data['server_id'] ?? null) && $zoneId) {
            $data['server_id'] = $zoneId;
        }

        $productName = $request->input('product_name');

        if (! $productName) {
            if ($data['game_type'] === 'mlbb') {
                $query = UserMlProduct::where('product_id', $data['product_id']);
                if (! empty($data['region'])) {
                    $query->where('region', $data['region']);
                }
                $productName = optional($query->first())->name;
            } elseif ($data['game_type'] === 'pubg') {
                $productName = optional(UserPubgProduct::where('product_id', $data['product_id'])->first())->name;
            } elseif ($data['game_type'] === 'mcgg') {
                $productName = optional(UserMcggProduct::where('product_id', $data['product_id'])->first())->name;
            } elseif ($data['game_type'] === 'wwm') {
                $productName = optional(UserWwmProduct::where('product_id', $data['product_id'])->first())->name;
            }
        }

        $data['product_name'] = $productName ?: '';

        $amount = 0;
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

        $quantity = (int) ($request->input('quantity') ?? 1);
        if ($quantity < 1) $quantity = 1;
        $data['quantity'] = $quantity;

        if ($amount > 0) {
            $amount = $amount * $quantity;
        }

        $data['amount'] = $amount > 0 ? $amount : $data['amount'];

        $data['status'] = 'pending';

        $methodKey = strtolower($data['payment_method'] ?? 'kpay');
        $data['payment_method'] = $methodKey;

        $methodLabel = strtoupper($methodKey);
        if (Schema::hasTable('payment_methods')) {
            $pm = PaymentMethod::whereRaw('LOWER(name) = ?', [$methodKey])->first();
            if ($pm) {
                $methodLabel = $pm->name;
            }
        }

        $normalizedPhone = preg_replace('/\D+/', '', (string) ($data['kpay_phone'] ?? ''));
        if ($normalizedPhone) {
            $data['kpay_phone'] = $normalizedPhone;
        }

        // Guest checkout logic: Find or create user by phone, then auto-login
        if ($normalizedPhone) {
            Log::info('Processing guest checkout user logic');
            if (Auth::check() && Auth::user()->role !== 'admin') {
                $data['user_id'] = Auth::id();
            } else {
                $user = User::where('phone', $normalizedPhone)->where('role', '!=', 'admin')->first();
                
                if (! $user) {
                    $email = $normalizedPhone.'@phone.local';
                    // Check for existing email to avoid collision if phone was different but email generated same
                    if (User::where('email', $email)->exists()) {
                        $email = $normalizedPhone.'+'.Str::random(6).'@phone.local';
                    }
                    
                    Log::info('Creating new guest user', ['phone' => $normalizedPhone]);
                    $user = User::create([
                        'name' => 'User'.substr($normalizedPhone, -4),
                        'email' => $email,
                        'phone' => $normalizedPhone,
                        'password' => Str::random(16), // Random password, user can't login unless they reset or we provide
                        'balance' => 0,
                        'role' => 'user',
                    ]);
                }
                
                Auth::login($user);
                $data['user_id'] = $user->id;
            }
        }

        KpayOrder::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('payment.success'),
                'message' => 'Payment submitted successfully!'
            ]);
        }

        return redirect()->route('payment.success')->with('success', 'Payment submitted successfully!');
    }

    public function adminIndex(Request $request)
    {
        $orders = $this->getFilteredOrders($request);

        $games = ['mlbb', 'pubg', 'mcgg', 'wwm'];
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        $paymentMethods = PaymentMethod::all();

        return view('admin.confirm_orders', compact('orders', 'games', 'statuses', 'paymentMethods'));
    }

    public function fetchOrders(Request $request)
    {
        $orders = $this->getFilteredOrders($request);

        // Render the partial view
        $html = view('admin.partials.confirm_orders_table', compact('orders'))->render();

        // Get pending count
        $pendingCount = KpayOrder::where('status', 'pending')->count();

        return response()->json([
            'html' => $html,
            'pending_count' => $pendingCount
        ]);
    }

    public function fetchPendingCount()
    {
        $count = KpayOrder::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    private function getFilteredOrders(Request $request)
    {
        $query = KpayOrder::latest();

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->game_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        return $query->paginate(20);
    }

    // New granular approval method for JS-based progress
    public function approveItem(Request $request, KpayOrder $order)
    {
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order not pending'], 400);
        }

        try {
            // Guest User Logic (Ensure user exists before buying)
            if (empty($order->user_id) && ! empty($order->kpay_phone)) {
                $normalizedPhone = preg_replace('/\D+/', '', (string) $order->kpay_phone);
                if ($normalizedPhone) {
                    $user = User::where('phone', $normalizedPhone)->where('role', '!=', 'admin')->first();
                    if (! $user) {
                        $email = $normalizedPhone.'@phone.local';
                        if (User::where('email', $email)->exists()) {
                            $email = $normalizedPhone.'+'.Str::random(6).'@phone.local';
                        }
                        $user = User::create([
                            'name' => 'User'.substr($normalizedPhone, -4),
                            'email' => $email,
                            'phone' => $normalizedPhone,
                            'password' => Str::random(16),
                            'balance' => 0,
                            'role' => 'user',
                        ]);
                    }
                    $order->user_id = $user->id;
                    $order->save();
                }
            }

            // Game Purchase Logic
            if ($order->game_type === 'mlbb') {
                $service = new SoGameService;
                // Optional: Check role again or assume it was checked? 
                // Let's check role to be safe, it's fast.
                $check = $service->checkRole($order->player_id, (string) ($order->server_id ?? ''));
                if (! $check) {
                    return response()->json(['success' => false, 'message' => 'Game account not found'], 400);
                }

                $result = $service->buyProduct(
                    $order->player_id,
                    (string) ($order->server_id ?? ''),
                    $order->product_id,
                    null,
                    1
                );

                if (! ($result['success'] ?? false)) {
                    $msg = $result['message'] ?? 'Unknown error';
                    return response()->json(['success' => false, 'message' => $msg], 500);
                }

            } elseif ($order->game_type === 'pubg') {
                $service = new LaravelPubgService;
                $check = $service->checkId($order->player_id);
                if (($check['result'] ?? 0) !== 1) {
                    return response()->json(['success' => false, 'message' => 'Invalid PUBG ID'], 400);
                }

                $result = $service->order($order->player_id, $order->product_id);
                if (! ($result['success'] ?? false)) {
                    $msg = $result['message'] ?? 'Unknown error';
                    return response()->json(['success' => false, 'message' => $msg], 500);
                }

            } elseif ($order->game_type === 'mcgg') {
                $service = new McggGameService;
                $check = $service->checkId($order->player_id, (string) ($order->server_id ?? ''));
                if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
                    return response()->json(['success' => false, 'message' => 'MCGG player not found'], 400);
                }

                $product = UserMcggProduct::where('product_id', $order->product_id)->where('status', 1)->first();
                if (!$product) return response()->json(['success' => false, 'message' => 'Product not found'], 400);

                $result = $service->order(
                    $order->player_id,
                    (string) ($order->server_id ?? ''),
                    $order->product_id,
                    null,
                    $product->diamonds
                );

                if (! ($result['ok'] ?? false)) {
                    $msg = $result['error'] ?? $result['message'] ?? 'Unknown error';
                    return response()->json(['success' => false, 'message' => $msg], 500);
                }

            } elseif ($order->game_type === 'wwm') {
                $service = new WwmGameService;
                $check = $service->checkId($order->player_id, (string) ($order->server_id ?? ''));
                if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
                    return response()->json(['success' => false, 'message' => 'WWM player not found'], 400);
                }

                $product = UserWwmProduct::where('product_id', $order->product_id)->where('status', 1)->first();
                if (!$product) return response()->json(['success' => false, 'message' => 'Product not found'], 400);

                $result = $service->order(
                    $order->player_id,
                    $order->product_id,
                    (string) ($order->server_id ?? ''),
                    null,
                    $product->diamonds,
                    null,
                    'usecoin',
                    1
                );

                if (! ($result['ok'] ?? false)) {
                    $msg = $result['error'] ?? $result['message'] ?? 'Unknown error';
                    return response()->json(['success' => false, 'message' => $msg], 500);
                }

            } else {
                return response()->json(['success' => false, 'message' => 'Unsupported game type'], 400);
            }

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function finalizeApproval(Request $request, KpayOrder $order)
    {
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order already processed'], 400);
        }

        try {
            $quantity = (int) ($order->quantity ?? 1);
            if ($quantity < 1) $quantity = 1;

            $sellingPrice = (int) ($order->amount ?? 0);
            $costPrice = 0;

            if ($order->game_type === 'mlbb') {
                $adminProductQuery = AdminMlProduct::where('product_id', $order->product_id);
                if (! empty($order->region)) {
                    $adminProductQuery->where('region', $order->region);
                }
                $adminProduct = $adminProductQuery->first();
                $costPrice = (int) ($adminProduct->price ?? 0);
            } elseif ($order->game_type === 'pubg') {
                $adminProduct = AdminPubgProduct::where('product_id', $order->product_id)->first();
                $costPrice = (int) ($adminProduct->price ?? 0);
            } elseif ($order->game_type === 'mcgg') {
                $adminProduct = AdminMcggProduct::where('product_id', $order->product_id)->first();
                $costPrice = (int) ($adminProduct->price ?? 0);
            } elseif ($order->game_type === 'wwm') {
                $adminProduct = AdminWwmProduct::where('product_id', $order->product_id)->first();
                $costPrice = (int) ($adminProduct->price ?? 0);
            }

            $costPrice = $costPrice * $quantity;
            $profit = $sellingPrice - $costPrice;

            if ($order->user_id && $sellingPrice > 0) {
                Order::create([
                    'user_id' => $order->user_id,
                    'game' => $order->game_type,
                    'product_id' => $order->product_id,
                    'product_name' => $order->product_name ?: $order->product_id,
                    'player_id' => $order->player_id,
                    'server_id' => $order->server_id,
                    'selling_price' => $sellingPrice,
                    'cost_price' => $costPrice,
                    'profit' => $profit,
                    'status' => 'success',
                ]);
            }

            $order->status = 'approved';
            $order->save();

            if ($order->user_id) {
                $game = strtoupper($order->game_type);
                $product = $order->product_name ?: $order->product_id;

                Notification::create([
                    'user_id' => $order->user_id,
                    'title' => 'Order Approved',
                    'message' => "Your {$game} order for {$product} has been approved and delivered.",
                    'type' => 'success',
                    'is_read' => false,
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function approve(KpayOrder $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be approved.');
        }

        $quantity = $order->quantity ?? 1;
        if ($quantity < 1) $quantity = 1;

        try {
            if (empty($order->user_id) && ! empty($order->kpay_phone)) {
                $normalizedPhone = preg_replace('/\D+/', '', (string) $order->kpay_phone);
                if ($normalizedPhone) {
                    $user = User::where('phone', $normalizedPhone)->where('role', '!=', 'admin')->first();
                    if (! $user) {
                        $email = $normalizedPhone.'@phone.local';
                        if (User::where('email', $email)->exists()) {
                            $email = $normalizedPhone.'+'.Str::random(6).'@phone.local';
                        }
                        $user = User::create([
                            'name' => 'User'.substr($normalizedPhone, -4),
                            'email' => $email,
                            'phone' => $normalizedPhone,
                            'password' => Str::random(16),
                            'balance' => 0,
                            'role' => 'user',
                        ]);
                    }
                    $order->user_id = $user->id;
                    $order->save();
                }
            }
            if ($order->game_type === 'mlbb') {
                $service = new SoGameService;
                $check = $service->checkRole($order->player_id, (string) ($order->server_id ?? ''));

                if (! $check) {
                    return redirect()->back()->with('error', 'Game account not found for this MLBB order.');
                }

                for ($i = 0; $i < $quantity; $i++) {
                    if ($i > 0) {
                        sleep(3); // Wait 3 seconds to prevent rate limiting
                    }
                    $result = $service->buyProduct(
                        $order->player_id,
                        (string) ($order->server_id ?? ''),
                        $order->product_id,
                        null,
                        1
                    );

                    if (! ($result['success'] ?? false)) {
                        $message = $result['message'] ?? 'Unknown error from game service.';
                        return redirect()->back()->with('error', 'MLBB order failed on item ' . ($i + 1) . ': ' . $message);
                    }
                }
            } elseif ($order->game_type === 'pubg') {
                $service = new LaravelPubgService;
                $check = $service->checkId($order->player_id);

                if (($check['result'] ?? 0) !== 1) {
                    return redirect()->back()->with('error', 'Invalid PUBG ID for this order.');
                }

                for ($i = 0; $i < $quantity; $i++) {
                    if ($i > 0) {
                        sleep(3);
                    }
                    $result = $service->order(
                        $order->player_id,
                        $order->product_id
                    );

                    if (! ($result['success'] ?? false)) {
                        $message = $result['message'] ?? 'Unknown error from game service.';
                        return redirect()->back()->with('error', 'PUBG order failed on item ' . ($i + 1) . ': ' . $message);
                    }
                }
            } elseif ($order->game_type === 'mcgg') {
                $service = new McggGameService;
                $check = $service->checkId($order->player_id, (string) ($order->server_id ?? ''));

                if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
                    $detail = $check['error'] ?? $check['message'] ?? 'Unknown error';
                    return redirect()->back()->with('error', 'MCGG player not found: '.$detail);
                }

                $product = UserMcggProduct::where('product_id', $order->product_id)->where('status', 1)->first();

                if (! $product) {
                    return redirect()->back()->with('error', 'MCGG product not found for this order.');
                }

                $amount = $product->diamonds;

                for ($i = 0; $i < $quantity; $i++) {
                    if ($i > 0) {
                        sleep(3);
                    }
                    $result = $service->order(
                        $order->player_id,
                        (string) ($order->server_id ?? ''),
                        $order->product_id,
                        null,
                        $amount
                    );

                    if (! ($result['ok'] ?? false)) {
                        $message = $result['error'] ?? $result['message'] ?? 'Unknown error from game service.';
                        return redirect()->back()->with('error', 'MCGG order failed on item ' . ($i + 1) . ': ' . $message);
                    }
                }
            } elseif ($order->game_type === 'wwm') {
                $service = new WwmGameService;
                $check = $service->checkId($order->player_id, (string) ($order->server_id ?? ''));

                if (! ($check['ok'] ?? false) && ! ($check['success'] ?? false)) {
                    $detail = $check['error'] ?? $check['message'] ?? 'Unknown error';
                    return redirect()->back()->with('error', 'WWM player not found: '.$detail);
                }

                $product = UserWwmProduct::where('product_id', $order->product_id)->where('status', 1)->first();

                if (! $product) {
                    return redirect()->back()->with('error', 'WWM product not found for this order.');
                }

                $amount = $product->diamonds;

                for ($i = 0; $i < $quantity; $i++) {
                    if ($i > 0) {
                        sleep(3);
                    }
                    $result = $service->order(
                        $order->player_id,
                        $order->product_id,
                        (string) ($order->server_id ?? ''),
                        null,
                        $amount,
                        null,
                        'usecoin',
                        1
                    );

                    if (! ($result['ok'] ?? false)) {
                        $message = $result['error'] ?? $result['message'] ?? 'Unknown error from game service.';
                        return redirect()->back()->with('error', 'WWM order failed on item ' . ($i + 1) . ': ' . $message);
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Unsupported game type for this order.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Approval failed: '.$e->getMessage());
        }

        $sellingPrice = (int) ($order->amount ?? 0);
        $costPrice = 0;

        if ($order->game_type === 'mlbb') {
            $adminProductQuery = AdminMlProduct::where('product_id', $order->product_id);
            if (! empty($order->region)) {
                $adminProductQuery->where('region', $order->region);
            }
            $adminProduct = $adminProductQuery->first();
            $costPrice = (int) ($adminProduct->price ?? 0);
        } elseif ($order->game_type === 'pubg') {
            $adminProduct = AdminPubgProduct::where('product_id', $order->product_id)->first();
            $costPrice = (int) ($adminProduct->price ?? 0);
        } elseif ($order->game_type === 'mcgg') {
            $adminProduct = AdminMcggProduct::where('product_id', $order->product_id)->first();
            $costPrice = (int) ($adminProduct->price ?? 0);
        } elseif ($order->game_type === 'wwm') {
            $adminProduct = AdminWwmProduct::where('product_id', $order->product_id)->first();
            $costPrice = (int) ($adminProduct->price ?? 0);
        }

        $costPrice = $costPrice * $quantity;
        $profit = $sellingPrice - $costPrice;

        if ($order->user_id && $sellingPrice > 0) {
            Order::create([
                'user_id' => $order->user_id,
                'game' => $order->game_type,
                'product_id' => $order->product_id,
                'product_name' => $order->product_name ?: $order->product_id,
                'player_id' => $order->player_id,
                'server_id' => $order->server_id,
                'selling_price' => $sellingPrice,
                'cost_price' => $costPrice,
                'profit' => $profit,
                'status' => 'success',
            ]);
        }

        $order->status = 'approved';
        $order->save();

        if ($order->user_id) {
            $game = strtoupper($order->game_type);
            $product = $order->product_name ?: $order->product_id;

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Order Approved',
                'message' => "Your {$game} order for {$product} has been approved and delivered.",
                'type' => 'success',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Order approved and submitted to game successfully.');
    }

    public function reject(KpayOrder $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be rejected.');
        }

        $order->status = 'cancelled';
        $order->save();

        if ($order->user_id) {
            $game = strtoupper($order->game_type);
            $product = $order->product_name ?: $order->product_id;

            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Order Rejected',
                'message' => "Your {$game} order for {$product} has been rejected. Please contact support.",
                'type' => 'error',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Order has been rejected.');
    }

    public function deleteOldOrders(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $days = $request->input('days');
        $date = \Carbon\Carbon::now()->subDays($days)->format('Y-m-d');

        $orders = KpayOrder::whereDate('created_at', '<', $date)->get();
        $count = 0;

        foreach ($orders as $order) {
            if ($order->transaction_image) {
                $imagePath = $order->transaction_image;
                // If it doesn't start with topups/ (old logic), append it
                // If it does (new logic), use as is
                if (!\Illuminate\Support\Str::startsWith($imagePath, 'topups/')) {
                    $imagePath = 'topups/' . $imagePath;
                }
                
                if (\Illuminate\Support\Facades\Storage::disk('adminimages')->exists($imagePath)) {
                    \Illuminate\Support\Facades\Storage::disk('adminimages')->delete($imagePath);
                }
            }
            $order->delete();
            $count++;
        }

        return redirect()->back()->with('success', "Deleted {$count} orders older than {$days} days.");
    }
}
