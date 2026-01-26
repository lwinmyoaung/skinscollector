<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminActivityController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\McggController;
use App\Http\Controllers\MLproductsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PubgController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WwmController;
use App\Models\GameImage;
use Illuminate\Support\Facades\Route;

// Route::resource('paymentmethod', ImageController::class); // Moved to admin middleware group


Route::get('/', function () {
    $gameImages = GameImage::all()->keyBy('game_code');
    return view('games', compact('gameImages'));
})->name('game.category');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/payment-methods', function () {
    $paymentMethods = \App\Models\PaymentMethod::all();
    return view('payment-methods', compact('paymentMethods'));
})->name('payment-methods');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard → /admin/dashboard
    Route::get('dashboard', [AccountController::class, 'advertiseIndex'])->name('admin.dashboard');
    Route::post('advertise', [AccountController::class, 'advertiseStore'])->name('admin.advertise.store');
    Route::delete('advertise/{filename}', [AccountController::class, 'advertiseDestroy'])->name('admin.advertise.destroy');
    Route::post('entry-ad', [AccountController::class, 'entryAdStore'])->name('admin.entry_ad.store');
    Route::delete('entry-ad', [AccountController::class, 'entryAdDestroy'])->name('admin.entry_ad.destroy');

    Route::get('cookie-and-api', [AccountController::class, 'cookieAndApiIndex'])->name('admin.cookieandapi');
    Route::post('cookie-and-api', [AccountController::class, 'cookieAndApiUpdate'])->name('admin.cookieandapi.update');

    // Users → /admin
    Route::get('/admin', [AccountController::class, 'showusers'])->name('admin.users');
    Route::post('/users/{id}/message', [AccountController::class, 'sendUserMessage'])->name('admin.users.message');
    Route::delete('/users/{id}', [AccountController::class, 'deleteUser'])->name('admin.users.delete');
    Route::put('/users/{id}/role', [AccountController::class, 'updateUserRole'])->name('admin.users.updateRole');

    // (Old Topups/Orders routes removed)

    Route::get('mlbb/prices', [AccountController::class, 'mlbbPrices'])->name('admin.mlbb.prices');
    Route::post('mlbb/prices', [AccountController::class, 'updateMlbbPrices'])->name('admin.mlbb.prices.update');
    Route::post('mlbb/prices/seed', [AccountController::class, 'seedMlbbPrices'])->name('admin.mlbb.prices.seed');
    Route::post('mlbb/prices/fetch', [AccountController::class, 'fetchMlbbFromApi'])->name('admin.mlbb.prices.fetch');
    Route::post('mlbb/prices/bulk', [AccountController::class, 'bulkUpdateMlbbPrices'])->name('admin.mlbb.prices.bulk');

    Route::get('pubg/prices', [AccountController::class, 'pubgPrices'])->name('admin.pubg.prices');
    Route::post('pubg/prices', [AccountController::class, 'updatePubgPrices'])->name('admin.pubg.prices.update');
    Route::post('pubg/prices/fetch', [AccountController::class, 'fetchPubgFromApi'])->name('admin.pubg.prices.fetch');
    Route::post('pubg/prices/bulk', [AccountController::class, 'bulkUpdatePubgPrices'])->name('admin.pubg.prices.bulk');

    Route::get('mcgg/prices', [AccountController::class, 'mcggPrices'])->name('admin.mcgg.prices');
    Route::post('mcgg/prices', [AccountController::class, 'updateMcggPrices'])->name('admin.mcgg.prices.update');
    Route::post('mcgg/prices/fetch', [AccountController::class, 'fetchMcggFromApi'])->name('admin.mcgg.prices.fetch');
    Route::post('mcgg/prices/bulk', [AccountController::class, 'bulkUpdateMcggPrices'])->name('admin.mcgg.prices.bulk');

    Route::get('wwm/prices', [AccountController::class, 'wwmPrices'])->name('admin.wwm.prices');
    Route::post('wwm/prices', [AccountController::class, 'updateWwmPrices'])->name('admin.wwm.prices.update');
    Route::post('wwm/prices/fetch', [AccountController::class, 'fetchWwmFromApi'])->name('admin.wwm.prices.fetch');
    Route::post('wwm/prices/bulk', [AccountController::class, 'bulkUpdateWwmPrices'])->name('admin.wwm.prices.bulk');

    // User Activity Logs
    Route::get('activity/logs', [AdminActivityController::class, 'index'])->name('admin.activity.logs');
    Route::delete('activity/logs/cleanup', [AdminActivityController::class, 'deleteOldLogs'])->name('admin.activity.delete_old');

    // (Old pending orders count removed)

    // Contact Manager
    Route::resource('contacts', AdminContactController::class)->except(['create', 'edit', 'show'])->names([
        'index' => 'admin.contacts.index',
        'store' => 'admin.contacts.store',
        'update' => 'admin.contacts.update',
        'destroy' => 'admin.contacts.destroy',
    ]);

    // Game Image Manager
    Route::resource('game-images', \App\Http\Controllers\Admin\GameImageController::class)
        ->only(['index', 'edit', 'update'])
        ->names([
            'index' => 'admin.game-images.index',
            'edit' => 'admin.game-images.edit',
            'update' => 'admin.game-images.update',
        ]);

    Route::get('/bank', [\App\Http\Controllers\Admin\BankController::class, 'index'])->name('admin.bank.index');
    Route::delete('/bank/cleanup', [\App\Http\Controllers\Admin\BankController::class, 'deleteOldOrders'])->name('admin.bank.delete_old');

    Route::resource('paymentmethod', ImageController::class);

    Route::get('kpay-orders', [\App\Http\Controllers\KpayOrderController::class, 'adminIndex'])->name('admin.kpay.orders');
    Route::delete('kpay-orders/cleanup', [\App\Http\Controllers\KpayOrderController::class, 'deleteOldOrders'])->name('admin.kpay.orders.delete_old');
    Route::get('kpay-orders/fetch', [\App\Http\Controllers\KpayOrderController::class, 'fetchOrders'])->name('admin.kpay.orders.fetch');
    Route::post('kpay-orders/{order}/approve', [\App\Http\Controllers\KpayOrderController::class, 'approve'])->name('admin.kpay.orders.approve');
    Route::post('kpay-orders/{order}/reject', [\App\Http\Controllers\KpayOrderController::class, 'reject'])->name('admin.kpay.orders.reject');
});

Route::get('/ml', [MLproductsController::class, 'fetchAndSave'])->name('mlproducts');
Route::get('/pubg-mobile', [PubgController::class, 'index'])->name('pubg');
Route::get('/mcgg', [McggController::class, 'index'])->name('mcgg');
Route::get('/wwm', [WwmController::class, 'index'])->name('wwm');
Route::post('/pubg/check-id', [PubgController::class, 'checkId'])->name('pubg.checkId');
Route::post('/mcgg/check-id', [McggController::class, 'checkId'])->name('mcgg.checkId');
Route::post('/wwm/check-id', [WwmController::class, 'checkId'])->name('wwm.checkId');
Route::post('/ml/check-id', [MLproductsController::class, 'checkId'])->name('ml.checkRole');
Route::post('/ordersubmit', [OrderController::class, 'submit'])->name('order.submit');
Route::post('/kpay/order', [\App\Http\Controllers\KpayOrderController::class, 'store'])->name('kpay.order.submit');
Route::post('/payment/start', [\App\Http\Controllers\PaymentController::class, 'start'])->name('payment.start');

Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [\App\Http\Controllers\KpayOrderController::class, 'userIndex'])->name('user.kpay.orders');
    Route::get('/inbox', [NotificationController::class, 'index'])->name('notifications.inbox');
});
Route::get('/register', [UserController::class, 'index'])->name('register');

Route::post('/login', [UserController::class, 'check'])->name('login.check');
Route::post('/register', [UserController::class, 'store'])->name('register.store');

Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->middleware('auth')->name('notifications.unread');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth')->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->middleware('auth')->name('notifications.markAllRead');

Route::get('/wallet', [UserController::class, 'wallet'])->middleware('auth')->name('user.wallet');
Route::get('/userwallet', [UserController::class, 'checkLoggedIn'])->name('userwallet');
Route::get('/wallet/topup', [TopupController::class, 'index'])->middleware('auth')->name('user.topup');
Route::post('/wallet/topup', [TopupController::class, 'store'])->middleware('auth')->name('user.topup.store');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/login', [LoginController::class, 'index'])->name('login');

// Product Categories
// Route::get('/game', [ProductController::class, 'gameCategory'])->name('game.category');
// Route::get('/card', [ProductController::class, 'cardCategory'])->name('card.category');
// Route::get('/mobile-recharge', [ProductController::class, 'mobileRecharge'])->name('mobile.recharge');
// Route::get('/wallet-top-up', [ProductController::class, 'walletTopUp'])->name('wallet.topup');

// Product Details
// Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Cart
Route::get('/shopping-cart', function () {
    return view('cart.index');
})->name('cart');

// Checkout
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

// Account Routes (TODO: These routes are broken or expose admin logic. Disabled for security.)
// Route::prefix('account')->group(function () {
//     Route::get('/', [AccountController::class, 'index'])->name('account');
//     Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
//     Route::get('/edit-account', [AccountController::class, 'edit'])->name('account.edit');
//     Route::get('/license-key', [AccountController::class, 'licenseKey'])->name('license.key');
// });

Route::fallback(function () {
    return redirect()->route('game.category');
});
