<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// routes/web.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/blacklist', [AdminController::class, 'blacklist'])->name('admin.blacklist');
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
});


Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('dashboard');
    Route::get('/number', [UsersController::class, 'number'])->name('user.number');
    Route::get('/transaction', [UsersController::class, 'transaction'])->name('user.transaction');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/status', [OrderController::class, 'checkStatus'])->name('order.checkStatus');
});
require __DIR__.'/auth.php';
