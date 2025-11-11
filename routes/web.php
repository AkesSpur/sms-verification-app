<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DigitalProductOrderController;
use App\Http\Controllers\GiftOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SmsRentalController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Gateways\PaystackController;
use App\Http\Controllers\UsaNumberController;
use App\Http\Controllers\InternationalNumberController;
use App\Http\Controllers\SocialMediaBoostingController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ResellerOrderController;
use App\Http\Controllers\Gateways\PaymentPointController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/account-boosting', [SocialMediaBoostingController::class, 'services'])->name('services');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::get('/product/{slug}', [HomeController::class, 'showProduct'])->name('product.show');
Route::get('/all-categories', [HomeController::class, 'allCategories'])->name('all-categories');
Route::get('/all-gifts', [HomeController::class, 'allGifts'])->name('all-gifts');
Route::get('/gift/{slug}', [HomeController::class, 'showGift'])->name('gift.show');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// routes/web.php
// Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
//     Route::get('/blacklist', [AdminController::class, 'blacklist'])->name('admin.blacklist');
//     Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
// });


Route::prefix('user')->middleware(['auth', 'verified', 'require.phone'])->group(function () {
    Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/usa-numbers', [UsersController::class, 'usaNumbers'])->name('user.usa-numbers');
    Route::get('/all-countries', [UsersController::class, 'allCountriesNumbers'])->name('user.all-countries');
    Route::get('/transaction', [UsersController::class, 'transaction'])->name('user.transaction');
    Route::get('/order-history', [UsersController::class, 'orderHistory'])->name('user.order-history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/status', [OrderController::class, 'checkStatus'])->name('order.checkStatus');
    
    // Gift Order Routes
    Route::post('/gift-order', [GiftOrderController::class, 'store'])->name('gift-order.store');
    Route::get('/gift-order/{giftOrder}', [GiftOrderController::class, 'show'])->name('gift-order.show');
    Route::get('/gift-order/{giftOrder}/status', [GiftOrderController::class, 'checkStatus'])->name('gift-order.checkStatus');
    
    // USA Number Routes (specialized controller)
    Route::prefix('usa')->group(function () {
        Route::post('/check-availability', [UsaNumberController::class, 'checkAvailability'])->name('usa.check-availability');
        Route::post('/purchase', [UsaNumberController::class, 'store'])->name('usa.purchase');
        Route::get('/order/{order}/status', [UsaNumberController::class, 'checkStatus'])->name('usa.order.status');
        Route::post('/order/{order}/cancel', [UsaNumberController::class, 'cancel'])->name('usa.order.cancel');
        Route::post('/order/{order}/resend-sms', [UsaNumberController::class, 'resendSms'])->name('usa.order.resend-sms');
        Route::get('/order/{order}', [UsaNumberController::class, 'show'])->name('usa.order.show');
    });
    
    // International Number Routes (specialized controller)
    Route::prefix('international')->group(function () {
        Route::post('/check-availability', [InternationalNumberController::class, 'checkAvailability'])->name('international.check-availability');
        Route::post('/purchase', [InternationalNumberController::class, 'store'])->name('international.purchase');
        Route::get('/order/{order}/status', [InternationalNumberController::class, 'checkStatus'])->name('international.order.status');
        Route::post('/order/{order}/cancel', [InternationalNumberController::class, 'cancelOrder'])->name('international.order.cancel');
        Route::get('/order/{order}', [InternationalNumberController::class, 'show'])->name('international.order.show');
    });

    //dasiy sms rental route
    Route::prefix('sms-rental')->name('user.sms.rental.')->group(function () {
        Route::get('/', [SmsRentalController::class, 'index'])->name('index');
        Route::post('rent', [SmsRentalController::class, 'rent'])->name('rent');
        Route::get('check-code/{id}', [SmsRentalController::class, 'checkCode'])->name('check.code');
        Route::post('cancel/{id}', [SmsRentalController::class, 'cancel'])->name('cancel');
        Route::post('auto-cancel/{id}', [SmsRentalController::class, 'autoCancel'])->name('auto.cancel');
        Route::get('history', [SmsRentalController::class, 'history'])->name('history');
        Route::get('details/{id}', [SmsRentalController::class, 'details'])->name('details');
        Route::get('services', [SmsRentalController::class, 'getServices'])->name('services');
        Route::get('countries', [SmsRentalController::class, 'getCountries'])->name('countries');
        Route::get('prices/{service}/{country}', [SmsRentalController::class, 'getPrices'])->name('prices');
    });
    
    // Social Media Boosting Routes
    Route::prefix('social-media-boosting')->group(function () {
        Route::get('/', [SocialMediaBoostingController::class, 'index'])->name('user.social-media-boosting.index');
        Route::get('/category/{slug}', [SocialMediaBoostingController::class, 'category'])->name('user.social-media-boosting.category');
        Route::get('/category/{categorySlug}/product/{productSlug}', [SocialMediaBoostingController::class, 'product'])->name('user.social-media-boosting.product');
        Route::post('/calculate-price/{product}', [SocialMediaBoostingController::class, 'calculatePrice'])->name('user.social-media-boosting.calculate-price');
        Route::post('/purchase/{product}', [SocialMediaBoostingController::class, 'purchase'])->name('user.social-media-boosting.purchase');
        Route::get('/orders', [SocialMediaBoostingController::class, 'orders'])->name('user.social-media-orders.index');
        Route::get('/orders/{order}', [SocialMediaBoostingController::class, 'showOrder'])->name('user.social-media-orders.show');
    });
    // Reseller store routes
    Route::get('/reseller', [ResellerController::class, 'index'])->name('user.reseller');
    Route::post('/reseller/request', [ResellerController::class, 'requestAccess'])->name('user.reseller.request');
    Route::post('/reseller/purchase', [ResellerOrderController::class, 'store'])->name('reseller.purchase');
});

// API routes for AJAX calls
Route::prefix('api')->group(function () {
    // Add API routes here if needed
    
    Route::prefix('user')->middleware('auth')->group(function () {
        Route::get('/transactions', [UsersController::class, 'getTransactions'])->name('api.user.transactions');
        Route::get('/digital-orders', [DigitalProductOrderController::class, 'getUserOrders'])->name('api.user.digital-orders');
        Route::get('/gift-orders', [GiftOrderController::class, 'getUserOrders'])->name('api.user.gift-orders');
        Route::get('/digital-orders/{id}', [DigitalProductOrderController::class, 'show'])->name('api.user.digital-orders.show');
        Route::get('/reseller-orders', [ResellerOrderController::class, 'getUserOrders'])->name('api.user.reseller-orders');
        Route::get('/reseller-orders/{order}', [ResellerOrderController::class, 'show'])->name('api.user.reseller-orders.show');
        Route::post('/set-deposit-amount', [UsersController::class, 'setDepositAmount'])->name('user.set-deposit-amount');

        // PaymentPoint Virtual Account API
        Route::get('/virtual-account', [PaymentPointController::class, 'getVirtualAccount'])->name('api.user.virtual-account.get');
        Route::post('/virtual-account/create', [PaymentPointController::class, 'createVirtualAccount'])->name('api.user.virtual-account.create');
    });
});

// Paystack routes
Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('paystack/redirect', [PaystackController::class, 'paystackRedirect'])->name('user.paystack.redirect');
    Route::get('paystack/callback', [PaystackController::class, 'verifyTransaction'])->name('user.paystack.callback');
});

// Etegram routes
// Route::prefix('user')->middleware('auth')->group(function () {
//     Route::post('etegram/redirect', [\App\Http\Controllers\Gateways\EtegramController::class, 'etegramRedirect'])->name('user.etegram.redirect');
//     Route::get('etegram/callback', [\App\Http\Controllers\Gateways\EtegramController::class, 'verifyTransaction'])->name('user.etegram.callback');
// });

// Digital Product Order routes
Route::prefix('digital-products')->middleware('auth')->group(function () {
    Route::post('/purchase', [DigitalProductOrderController::class, 'store'])->name('digital-products.purchase');
});

// PaymentPoint webhook (no auth, exclude CSRF)
Route::post('/webhook/paymentpoint', [PaymentPointController::class, 'webhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.paymentpoint');
require __DIR__.'/auth.php';
