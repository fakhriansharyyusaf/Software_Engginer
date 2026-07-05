<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\BuyerController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\AdminController;

// ---------- Public ----------
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{product}', [CatalogController::class, 'productDetail']);
Route::get('/catalog/stores/{store}', [CatalogController::class, 'storeDetail']);

// ---------- Authenticated (any role) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/active-role', [AuthController::class, 'setActiveRole']);

    // ---------- Seller ----------
    Route::middleware('api_role:Seller')->prefix('seller')->group(function () {
        Route::get('/store', [SellerController::class, 'storeShow']);
        Route::post('/store', [SellerController::class, 'storeSave']);
        Route::get('/products', [SellerController::class, 'products']);
        Route::post('/products', [SellerController::class, 'productStore']);
        Route::put('/products/{product}', [SellerController::class, 'productUpdate']);
        Route::delete('/products/{product}', [SellerController::class, 'productDelete']);
        Route::get('/orders', [SellerController::class, 'orders']);
        Route::post('/orders/{order}/process', [SellerController::class, 'processOrder']);
    });

    // ---------- Buyer ----------
    Route::middleware('api_role:Buyer')->prefix('buyer')->group(function () {
        Route::get('/wallet', [BuyerController::class, 'wallet']);
        Route::post('/wallet/topup', [BuyerController::class, 'topUp']);
        Route::get('/addresses', [BuyerController::class, 'addresses']);
        Route::post('/addresses', [BuyerController::class, 'addressStore']);
        Route::put('/addresses/{address}', [BuyerController::class, 'addressUpdate']);
        Route::delete('/addresses/{address}', [BuyerController::class, 'addressDelete']);
        Route::get('/cart', [BuyerController::class, 'cart']);
        Route::post('/cart/items', [BuyerController::class, 'cartAdd']);
        Route::put('/cart/items/{cartItemId}', [BuyerController::class, 'cartUpdate']);
        Route::post('/cart/clear', [BuyerController::class, 'cartClear']);
        Route::post('/checkout', [BuyerController::class, 'checkout']);
        Route::get('/orders', [BuyerController::class, 'orders']);
        Route::get('/orders/{order}', [BuyerController::class, 'orderDetail']);
    });

    // ---------- Driver ----------
    Route::middleware('api_role:Driver')->prefix('driver')->group(function () {
        Route::get('/jobs', [DriverController::class, 'availableJobs']);
        Route::get('/jobs/{job}', [DriverController::class, 'jobDetail']);
        Route::post('/jobs/{job}/take', [DriverController::class, 'takeJob']);
        Route::post('/jobs/{job}/complete', [DriverController::class, 'completeJob']);
        Route::get('/my-jobs', [DriverController::class, 'myJobs']);
    });

    // ---------- Admin ----------
    Route::middleware('api_role:Admin')->prefix('admin')->group(function () {
        Route::get('/monitoring', [AdminController::class, 'monitoring']);
        Route::get('/vouchers', [AdminController::class, 'vouchers']);
        Route::post('/vouchers', [AdminController::class, 'voucherStore']);
        Route::get('/vouchers/{voucher}', [AdminController::class, 'voucherShow']);
        Route::get('/promos', [AdminController::class, 'promos']);
        Route::post('/promos', [AdminController::class, 'promoStore']);
        Route::get('/promos/{promo}', [AdminController::class, 'promoShow']);
        Route::post('/time/simulate-next-day', [AdminController::class, 'simulateNextDay']);
        Route::post('/overdue/run', [AdminController::class, 'runOverdueCheck']);
    });
});
