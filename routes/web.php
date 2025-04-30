<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NumberController;

// Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Breeze Authentication Routes
require __DIR__.'/auth.php';

// ================= USER ROUTES ===================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [ShopController::class, 'dashboard'])->name('dashboard');

    // Products
    Route::get('/product/{id}', [ShopController::class, 'product'])->name('product');
    Route::post('/purchase/{id}', [ShopController::class, 'purchase'])->name('purchase');
    Route::get('/sync-categories', [ShopController::class, 'syncCategories'])->name('sync.categories');
    Route::get('/sync-products', [ShopController::class, 'syncProducts'])->name('sync.products');
    Route::get('/manual-order/{id}', [ShopController::class, 'manualOrder'])->name('manual.order');

    // Orders
    Route::get('/orders', [ShopController::class, 'userOrders'])->name('orders');
    Route::get('/order/{api_order_id}', [ShopController::class, 'order'])->name('order');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/fund', [WalletController::class, 'fund'])->name('wallet.fund');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tellabot
    Route::get('/tellabot/services', [NumberController::class, 'tellabotServices'])->name('tellabot.services');
    Route::post('/server2/purchase', [NumberController::class, 'server2Purchase'])->name('server2.purchase');
    Route::get('/tellabot/check/{id}', [NumberController::class, 'tellabotCheckCode'])->name('tellabot.check');
    Route::post('/tellabot/cancel/{id}', [NumberController::class, 'tellabotCancel'])->name('tellabot.cancel');
   


    // Tellabot MDN/Code Check
    Route::get('/number/check-code/tellabot/{id}', [NumberController::class, 'tellabotCheckCode'])->name('tellabot.checkCode');
    Route::get('/number/check-mdn/tellabot/{id}', [NumberController::class, 'checkTellabotMdn'])->name('tellabot.check-mdn');
    Route::get('/number/sync-tellabot-orders', [NumberController::class, 'syncTellabotOrders'])->name('tellabot.sync');
    Route::get('/number/sync-mdns', [NumberController::class, 'syncTellabotMdnsOneByOne'])->name('tellabot.syncMdns');

    Route::get('/number/sync-activation-mdns', [NumberController::class, 'syncTellabotMdnsByActivationId'])->name('tellabot.syncActivationMdns');

    Route::get('/number/sync-mdn-service', [NumberController::class, 'syncTellabotMdnsWithService']);



    Route::get('/number/check-status/tellabot/{id}', [NumberController::class, 'checkTellabotStatus']);

    


    // Number Services (Daisy/Oprime)
    Route::get('/number', [NumberController::class, 'services'])->name('number.services');
    Route::post('/number/purchase', [NumberController::class, 'purchase'])->name('number.purchase');
    Route::post('/number/cancel/{id}', [NumberController::class, 'cancel'])->name('number.cancel');
    Route::get('/number/check-code/{id}', [NumberController::class, 'checkCode'])->name('number.check-code');
    Route::post('/number/webhook', [NumberController::class, 'webhook'])->name('number.webhook');
});

























// ================= ADMIN ROUTES ===================
Route::prefix('admin')->group(function () {

    // Admin Auth
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');

    Route::middleware('auth:admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Categories
        Route::get('/categories', [AdminController::class, 'manageCategories'])->name('admin.categories');
        Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
        Route::post('/categories/store', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
        Route::put('/categories/{id}/update', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/categories/{id}/delete', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');

        // Products
        Route::get('/products', [AdminController::class, 'manageProducts'])->name('admin.products');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.product.store');
        Route::get('/products/upload', [AdminController::class, 'showUploadForm'])->name('admin.products.upload.form');
        Route::post('/products/upload', [AdminController::class, 'uploadProducts'])->name('admin.products.upload');
        Route::get('/products/list', [AdminController::class, 'listProducts'])->name('admin.products.index');
        Route::get('/products/{id}/add-stock', [AdminController::class, 'addStockForm'])->name('admin.products.addStockForm');
        Route::post('/products/{id}/add-stock', [AdminController::class, 'storeStock'])->name('admin.products.storeStock');
        Route::delete('/products/{id}/delete', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
        Route::delete('/products/api/{id}/delete', [AdminController::class, 'deleteApiProduct'])->name('admin.products.api.delete');

        // Orders
        Route::get('/orders', [AdminController::class, 'manageOrders'])->name('admin.orders');
        Route::get('/orders/{id}/view', [AdminController::class, 'viewOrder'])->name('admin.order.view');

        // Transactions
        Route::get('/transactions', [AdminController::class, 'manageTransactions'])->name('admin.transactions');

        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

        // Users
        Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users');
        Route::get('/user/{id}', [AdminController::class, 'viewUser'])->name('admin.user.view');
        Route::post('/user/{id}/fund', [AdminController::class, 'fundUser'])->name('admin.user.fund');
        Route::post('/user/{id}/debit', [AdminController::class, 'debitUser'])->name('admin.user.debit');
        Route::post('/user/{id}/ban', [AdminController::class, 'banUser'])->name('admin.user.ban');
        Route::post('/user/{id}/unban', [AdminController::class, 'unbanUser'])->name('admin.user.unban');
        Route::post('/user/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.user.reset-password');
        Route::get('/user/{id}/orders', [AdminController::class, 'userOrders'])->name('admin.user.orders');
        Route::get('/user/{id}/transactions', [AdminController::class, 'userWalletTransactions'])->name('admin.user.transactions');
    });


    
}); 