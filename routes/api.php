<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateways\PaymentPointController;

// Authenticated user endpoint (default example)
Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

// PaymentPoint webhook endpoint (auto-prefixed with /api)
// Final URL: /api/webhook/paymentpoint
Route::post('/webhook/paymentpoint', [PaymentPointController::class, 'webhook'])
    ->name('api.webhook.paymentpoint');