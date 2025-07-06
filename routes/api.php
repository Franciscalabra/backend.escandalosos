<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ComboController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {

    // 🛍️ Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/categories', [ProductController::class, 'categories']);

    // 🛒 Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{id}', [CartController::class, 'updateItem']);
        Route::delete('/items/{id}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });

    // 🚚 Delivery
    Route::get('/delivery/zones', [DeliveryController::class, 'zones']);
    Route::post('/delivery/calculate', [DeliveryController::class, 'calculateFee']);

    // 🎁 Combos
    Route::get('/combos', [ComboController::class, 'index']);
    Route::get('/combos/rules', [ComboController::class, 'rules']);
    Route::post('/combos/check-eligibility', [ComboController::class, 'checkEligibility']);

    // 💳 Checkout
    Route::post('/checkout/process', [CheckoutController::class, 'process']);
    Route::post('/checkout/flow/confirm', [CheckoutController::class, 'confirmFlow']);

    // 📦 Order tracking (public)
    Route::get('/orders/track/{orderNumber}', [OrderController::class, 'track']);
});

// 🔒 Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
});

// 🔔 Webhook routes (exclude from CSRF)
Route::prefix('webhooks')->group(function () {
    Route::post('/flow', [CheckoutController::class, 'confirmFlow'])->name('webhooks.flow');
});
Route::get('/v1/test', function () {
    return response()->json(['status' => 'ok']);
});
