<?php

namespace App\Http\Controllers;

use App\Models\KpayOrder;
use App\Models\Order;
use App\Models\AdminMlProduct;
use App\Models\AdminPubgProduct;
use App\Models\AdminMcggProduct;
use App\Models\AdminWwmProduct;
use App\Models\Notification;
use App\Models\UserMlProduct;
use App\Models\UserPubgProduct;
use App\Models\UserMcggProduct;
use App\Models\UserWwmProduct;
use App\Models\PaymentMethod;
use App\Services\SoGameService;
use App\Services\LaravelPubgService;
use App\Services\McggGameService;
use App\Services\WwmGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentConfirmController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Payment submission started', ['phone' => $request->input('kpay_phone') ?? 'N/A']);
            $startTime = microtime(true);

            $validator = Validator::make($request->all(), [
                'game_type' => 'required|string|in:mlbb,pubg,mcgg,wwm',
                'product_id' => 'required|string',
                'player_id' => 'required|string',
                'server_id' => 'nullable|string',
                'region' => 'nullable|string',
                'payment_method' => 'required|string|max:50',
                'kpay_phone' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'transaction_image' => 'required|image|max:51200',
                'quantity' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                Log::warning('Order validation failed', ['errors' => $validator->errors()->toArray(), 'input' => $request->except('transaction_image')]);
                return redirect()->route('payment.retry')
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $validator->validated();

            if ($request->hasFile('transaction_image')) {
                $file = $request->file('transaction_image');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                
                // Direct upload to adminimages/topups folder
                $destinationPath = public_path('adminimages/topups');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                
                $data['transaction_image'] = $filename;
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
                        Log::info('Creating new guest user');
                        $email = $normalizedPhone.'@phone.local';
                        if (User::where('email', $email)->exists()) {
                            $email = $normalizedPhone.'+'.Str::random(6).'@phone.local';
                        }
                        
                        $user = User::create([
                            'name' => 'User'.substr($normalizedPhone, -4),
                            'email' => $email,
                            'phone' => $normalizedPhone,
                            'password' => \Illuminate\Support\Facades\Hash::make(Str::random(16)),
                            'role' => 'user',
                        ]);
                    }
                    
                    Log::info('Logging in user: ' . $user->id);
                    Auth::login($user, true);
                    $data['user_id'] = $user->id;
                }
            } else {
                $data['user_id'] = Auth::id(); 
            }

            Log::info('Creating KpayOrder');
            KpayOrder::create($data);
            Log::info('KpayOrder created');

            if (!empty($data['user_id'])) {
                Log::info('Creating notification');
                $game = strtoupper($data['game_type']);
                $product = $data['product_name'] ?: $data['product_id'];
                
                Notification::create([
                    'user_id' => $data['user_id'],
                    'title' => 'Order Submitted',
                    'message' => "Your {$game} order for {$product} has been submitted. Status: Pending.",
                    'type' => 'info',
                    'is_read' => false,
                ]);
            }

            Log::info('Order processing completed in ' . (microtime(true) - $startTime) . 's');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your '.$methodLabel.' order has been submitted. We will process it shortly.',
                    'redirect_url' => route('payment.success')
                ]);
            }

            return redirect()->route('payment.success');

        } catch (\Exception $e) {
            Log::error('Order submission error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'input' => $request->except('transaction_image')]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your request. Please try again.'
                ], 500);
            }

            return redirect()->route('payment.retry')
                ->with('error', 'An error occurred while processing your request. Please try again.')
                ->withInput();
        }
    }

    public function adminIndex(Request $request)
    {
        $query = KpayOrder::query()->latest();

        $hasPaymentMethodColumn = Schema::hasColumn('kpay_orders', 'payment_method');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->input('game_type'));
        }

        if ($hasPaymentMethodColumn && $request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $orders = $query->paginate(25);

        $statuses = ['pending', 'approved', 'failed', 'cancelled'];
        $games = ['mlbb', 'pubg', 'mcgg', 'wwm'];

        $paymentMethods = $hasPaymentMethodColumn ? PaymentMethod::all() : collect();

        return view('admin.confirm_orders', compact('orders', 'statuses', 'games', 'paymentMethods'));
    }

    public function userIndex(Request $request)
    {
        $user = Auth::user();

        $query = KpayOrder::query()
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->input('game_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(15);

        $statuses = ['pending', 'approved', 'failed', 'cancelled'];
        $games = ['mlbb', 'pubg', 'mcgg', 'wwm'];

        return view('user.kpay_orders', compact('orders', 'statuses', 'games'));
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

                $result = $service->buyProduct(
                    $order->player_id,
                    (string) ($order->server_id ?? ''),
                    $order->product_id,
                    null,
                    $quantity
                );

                if (! ($result['success'] ?? false)) {
                    $message = $result['message'] ?? 'Unknown error from game service.';
                    return redirect()->back()->with('error', 'MLBB order failed: ' . $message);
                }
            } elseif ($order->game_type === 'pubg') {
                $service = new LaravelPubgService;
                $check = $service->checkId($order->player_id);

                if (($check['result'] ?? 0) !== 1) {
                    return redirect()->back()->with('error', 'Invalid PUBG ID for this order.');
                }

                for ($i = 0; $i < $quantity; $i++) {
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

                $result = $service->order(
                    $order->player_id,
                    $order->product_id,
                    (string) ($order->server_id ?? ''),
                    null,
                    $amount,
                    null,
                    'usecoin',
                    $quantity
                );

                if (! ($result['ok'] ?? false)) {
                    $message = $result['error'] ?? $result['message'] ?? 'Unknown error from game service.';
                    return redirect()->back()->with('error', 'WWM order failed: ' . $message);
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
                $imagePath = 'topups/' . $order->transaction_image;
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
            $order->delete();
            $count++;
        }

        return redirect()->back()->with('success', "Deleted {$count} orders older than {$days} days.");
    }

    public function fetchOrders(Request $request)
    {
        $query = KpayOrder::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->input('game_type'));
        }

        if (Schema::hasColumn('kpay_orders', 'payment_method') && $request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $orders = $query->paginate(25);
        $pendingCount = KpayOrder::where('status', 'pending')->count();

        return response()->json([
            'html' => view('admin.partials.confirm_orders_table', compact('orders'))->render(),
            'pending_count' => $pendingCount,
        ]);
    }

    public function fetchPendingList(Request $request)
    {
        $orders = KpayOrder::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get(['id', 'game_type', 'product_name', 'product_id', 'amount', 'created_at', 'kpay_phone', 'payment_method']);

        $count = KpayOrder::where('status', 'pending')->count();

        return response()->json([
            'count' => $count,
            'orders' => $orders,
        ]);
    }
}