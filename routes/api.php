<?php

use App\Http\Controllers\GetATextWebhookController;
use App\Http\Controllers\Gateways\PaymentPointController;
use App\Http\Controllers\Api\ProductImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authenticated user endpoint (default example)
Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

// PaymentPoint webhook endpoint (auto-prefixed with /api)
// Final URL: /api/webhook/paymentpoint
Route::post('/webhook/paymentpoint', [PaymentPointController::class, 'webhook'])
    ->name('api.webhook.paymentpoint');

// GetAText webhook endpoint
// Final URL: /api/webhook/getatext
Route::post('/webhook/getatext', [GetATextWebhookController::class, 'webhook'])
    ->name('api.webhook.getatext');

// Public: list digital product images
// GET /api/products/images
Route::get('/products/images', [ProductImageController::class, 'index'])
    ->name('api.products.images');