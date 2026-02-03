<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminActivityController;
use App\Http\Controllers\AdminContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\McggController;
use App\Http\Controllers\MLproductsController;
use App\Http\Controllers\PaymentConfirmController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PubgController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WwmController;
use App\Models\GameImage;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('game.category');

Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/payment-methods', [HomeController::class, 'paymentMethods'])->name('payment-methods');

// Cache Management Routes (Publicly accessible as per user request for curl/cron usage)
Route::post('/refresh_cache', [AccountController::class, 'refreshCache'])->name('api.refresh_cache');
Route::get('/cache/status', [AccountController::class, 'getCacheStatus'])->name('api.cache.status');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard (Orders) -> /admin/dashboard
    Route::get('dashboard', [\App\Http\Controllers\PaymentConfirmController::class, 'adminIndex'])->name('admin.dashboard');

    // Ads Manager -> /admin/ads
    Route::get('ads', [AccountController::class, 'advertiseIndex'])->name('admin.ads');
    Route::post('advertise', [AccountController::class, 'advertiseStore'])->name('admin.advertise.store');
    Route::delete('advertise/{filename}', [AccountController::class, 'advertiseDestroy'])->name('admin.advertise.destroy');
    Route::post('entry-ad', [AccountController::class, 'entryAdStore'])->name('admin.entry_ad.store');
    Route::delete('entry-ad', [AccountController::class, 'entryAdDestroy'])->name('admin.entry_ad.destroy');
    Route::post('app-icon', [AccountController::class, 'appIconStore'])->name('admin.app_icon.store');
    Route::delete('app-icon', [AccountController::class, 'appIconDestroy'])->name('admin.app_icon.destroy');

    Route::get('cookie-and-api', [AccountController::class, 'cookieAndApiIndex'])->name('admin.cookieandapi');
    Route::post('cookie-and-api', [AccountController::class, 'cookieAndApiUpdate'])->name('admin.cookieandapi.update');

    // Users -> /admin
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

    // Error Logs
    Route::get('error-logs', [\App\Http\Controllers\Admin\ErrorLogController::class, 'index'])->name('admin.error-logs.index');
    Route::post('error-logs/fetch', [\App\Http\Controllers\Admin\ErrorLogController::class, 'fetch'])->name('admin.error-logs.fetch');
    Route::get('error-logs/{errorLog}', [\App\Http\Controllers\Admin\ErrorLogController::class, 'show'])->name('admin.error-logs.show');
    Route::delete('error-logs/clear', [\App\Http\Controllers\Admin\ErrorLogController::class, 'clear'])->name('admin.error-logs.clear');
    Route::delete('error-logs/{errorLog}', [\App\Http\Controllers\Admin\ErrorLogController::class, 'destroy'])->name('admin.error-logs.destroy');

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

    // Bank & Profit
    Route::get('bank', [\App\Http\Controllers\Admin\BankController::class, 'index'])->name('admin.bank.index');
    Route::post('bank/add-profit', [\App\Http\Controllers\Admin\BankController::class, 'addProfit'])->name('admin.bank.add-profit');
    Route::post('bank/withdraw-profit', [\App\Http\Controllers\Admin\BankController::class, 'withdrawProfit'])->name('admin.bank.withdraw-profit');
    Route::post('bank/reset-profit', [\App\Http\Controllers\Admin\BankController::class, 'resetProfit'])->name('admin.bank.reset-profit');
    Route::delete('bank/cleanup', [\App\Http\Controllers\Admin\BankController::class, 'deleteOldOrders'])->name('admin.bank.delete_old');

    // Payment Methods (ImageController)
    Route::resource('paymentmethod', ImageController::class);

    // Confirm Orders (Previously Kpay Orders)
    Route::get('confirm-orders', [\App\Http\Controllers\PaymentConfirmController::class, 'adminIndex'])->name('admin.confirm.orders');
    Route::delete('confirm-orders/cleanup', [\App\Http\Controllers\PaymentConfirmController::class, 'deleteOldOrders'])->name('admin.confirm.orders.delete_old');
    Route::get('confirm-orders/fetch', [\App\Http\Controllers\PaymentConfirmController::class, 'fetchOrders'])->name('admin.confirm.orders.fetch');
    Route::post('confirm-orders/{order}/approve', [\App\Http\Controllers\PaymentConfirmController::class, 'approve'])->name('admin.confirm.orders.approve');
    Route::post('confirm-orders/{order}/approve-item', [\App\Http\Controllers\PaymentConfirmController::class, 'approveItem'])->name('admin.confirm.orders.approve_item');
    Route::post('confirm-orders/{order}/finalize', [\App\Http\Controllers\PaymentConfirmController::class, 'finalizeApproval'])->name('admin.confirm.orders.finalize');
    Route::post('confirm-orders/{order}/reject', [\App\Http\Controllers\PaymentConfirmController::class, 'reject'])->name('admin.confirm.orders.reject');
});

// User routes
Route::post('/payment/start', [\App\Http\Controllers\PaymentController::class, 'start'])->name('payment.start');
Route::post('/payment/submit', [\App\Http\Controllers\PaymentConfirmController::class, 'store'])->name('payment.submit');
Route::view('/payment-success', 'payment-success')->name('payment.success');
Route::get('/payment/retry', [\App\Http\Controllers\PaymentController::class, 'retry'])->name('payment.retry');

Route::middleware(['auth'])->group(function () {
    Route::get('/orders/history', [\App\Http\Controllers\PaymentConfirmController::class, 'userIndex'])->name('user.kpay.orders');
    Route::get('/orders/history/fetch', [\App\Http\Controllers\PaymentConfirmController::class, 'fetchUserOrders'])->name('user.kpay.orders.fetch');
    Route::get('/wallet', [UserController::class, 'wallet'])->name('userwallet');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.inbox');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

Route::get('/mlbb', [MLproductsController::class, 'index'])->name('mlproducts');
Route::post('/mlbb/check-role', [MLproductsController::class, 'checkId'])->name('ml.checkRole');
Route::get('/pubg', [PubgController::class, 'index'])->name('pubg');
Route::post('/pubg/check-id', [PubgController::class, 'checkId'])->name('pubg.checkId');
Route::get('/mcgg', [McggController::class, 'index'])->name('mcgg');
Route::post('/mcgg/check-id', [McggController::class, 'checkId'])->name('mcgg.checkId');
Route::get('/wwm', [WwmController::class, 'index'])->name('wwm');
Route::post('/wwm/check-id', [WwmController::class, 'checkId'])->name('wwm.checkId');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'check'])->name('login.check');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [UserController::class, 'index'])->name('register');
Route::post('/register', [UserController::class, 'store'])->name('register.store');

// Image serving
Route::get('/images/{filename}', [ImageController::class, 'show'])->name('images.show');
Route::get('/storage/game_images/{filename}', function ($filename) {
    $path = storage_path('app/public/game_images/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('filename', '.*');

// Route for fetching account info
Route::post('/api/fetch-account-info', [AccountController::class, 'fetchAccountInfo']);
