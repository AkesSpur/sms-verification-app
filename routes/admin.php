<?php
use App\Http\Controllers\Backend\LocalBankSettingController;
use App\Http\Controllers\Backend\PaymentSettingController;
use App\Http\Controllers\Backend\PaystackSettingController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\AdminListController;
use App\Http\Controllers\Backend\CustomerListController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\FundManagementController;
use App\Http\Controllers\Backend\ManageUserController;
use App\Http\Controllers\Backend\CountryServiceController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\DigitalProductCategoryController;
use App\Http\Controllers\Backend\DigitalProductSubcategoryController;
use App\Http\Controllers\Backend\DigitalProductController;
use App\Http\Controllers\Backend\DigitalProductLogController;
use App\Http\Controllers\Backend\DigitalProductOrderController;
use App\Http\Controllers\Backend\GiftController;
use App\Http\Controllers\Backend\BannerController;
use Illuminate\Support\Facades\Route;


/* Dashboard route */
Route::get('/', [AdminController::class, 'index'])->name('dashboard');


/** customer list routes */
Route::put('customer/status-change', [CustomerListController::class, 'changeStatus'])->name('customer.status-change');
Route::put('customer-list/{id}/update-email', [CustomerListController::class, 'updateEmail'])->name('customer-list.update-email');
Route::get('customer', [CustomerListController::class, 'index'])->name('customer.index');
Route::post('customers/{id}/verify-email', [CustomerListController::class, 'verifyEmail'])->name('customers.verify-email');
Route::post('customers/{id}/send-reset-link', [CustomerListController::class, 'sendResetLink'])->name('customers.send-reset-link');
Route::delete('customers/{id}', [CustomerListController::class, 'destroy'])->name('customers.destroy');

/** admin list routes */
Route::get('admin-list', [AdminListController::class, 'index'])->name('admin-list.index');
Route::put('admin-list/status-change', [AdminListController::class, 'changeStatus'])->name('admin-list.status-change');
Route::put('admin-list/{id}/update-email', [AdminListController::class, 'updateEmail'])->name('admin-list.update-email');
Route::delete('admin-list/{id}', [AdminListController::class, 'destroy'])->name('admin-list.destroy');
Route::post('admins/{id}/verify-email', [AdminListController::class, 'verifyEmail'])->name('admins.verify-email');
Route::post('admins/{id}/send-reset-link', [AdminListController::class, 'sendResetLink'])->name('admins.send-reset-link');


/** Add and withdraw funds routes */
Route::get('add-fund/{id}', [FundManagementController::class, 'addIndex'])->name('add-fund.index');
Route::post('fund-user/{id}', [FundManagementController::class, 'addFund'])->name('fund-user');
Route::get('withdraw-fund/{id}', [FundManagementController::class, 'withdrawIndex'])->name('withdraw-fund.index');
Route::post('withdraw-user-fund/{id}', [FundManagementController::class, 'withdrawFund'])->name('withdraw-user-fund');

/** manage user routes */
Route::get('manage-user', [ManageUserController::class, 'index'])->name('manage-user.index');
Route::post('manage-user', [ManageUserController::class, 'create'])->name('manage-user.create');


/* Settings Routes */
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('general-setting-update', [SettingController::class, 'updateGeneralSetting'])->name('general-setting-update');
Route::put('email-setting-update', [SettingController::class, 'updateEmailSetting'])->name('email-setting-update');
Route::put('logo-setting-update', [SettingController::class, 'updateLogoSetting'])->name('logo-setting-update');

/* Country Service Pricing Routes */
Route::get('country-service', [CountryServiceController::class, 'index'])->name('country-service.index');
Route::get('country-service/{country}/prices', [CountryServiceController::class, 'getCountryPrices'])->name('country-service.prices');
Route::post('country-service/update-price', [CountryServiceController::class, 'updatePrice'])->name('country-service.update-price');
Route::delete('country-service/remove-price', [CountryServiceController::class, 'removeCustomPrice'])->name('country-service.remove-price');
Route::post('country-service/bulk-update', [CountryServiceController::class, 'bulkUpdatePrices'])->name('country-service.bulk-update');
Route::post('country-service/{country}/sync-api', [CountryServiceController::class, 'syncApiPrices'])->name('country-service.sync-api');

/* Service Management Routes */
Route::post('services/bulk-action', [ServiceController::class, 'bulkAction'])->name('services.bulk-action');
Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
Route::resource('services', ServiceController::class);

/* Digital Product Management Routes */
Route::resource('digital-product-categories', DigitalProductCategoryController::class);
Route::get('digital-product-subcategories/by-category/{category}', [DigitalProductSubcategoryController::class, 'getByCategory'])->name('digital-product-subcategories.by-category');
Route::resource('digital-product-subcategories', DigitalProductSubcategoryController::class);
Route::resource('digital-products', DigitalProductController::class);

/* Digital Product Log Management Routes */
Route::get('digital-product-logs/by-product/{product}', [DigitalProductLogController::class, 'getByProduct'])->name('digital-product-logs.by-product');
Route::get('digital-product-logs/add-logs', [DigitalProductLogController::class, 'showAddLogsForm'])->name('digital-product-logs.add-logs');
Route::post('digital-product-logs/add-logs', [DigitalProductLogController::class, 'addLogs'])->name('digital-product-logs.store-multiple');
Route::post('digital-product-logs/{digitalProductLog}/mark-available', [DigitalProductLogController::class, 'markAsAvailable'])->name('digital-product-logs.mark-available');
Route::resource('digital-product-logs', DigitalProductLogController::class);

/* Digital Product Order Management Routes */
Route::get('digital-product-orders/export', [DigitalProductOrderController::class, 'export'])->name('digital-product-orders.export');
Route::put('digital-product-orders/{order}/status', [DigitalProductOrderController::class, 'updateStatus'])->name('digital-product-orders.update-status');
Route::resource('digital-product-orders', DigitalProductOrderController::class)->only(['index', 'show', 'destroy']);

/* Gift Management Routes */
Route::delete('gifts/images/{image}', [GiftController::class, 'deleteImage'])->name('gifts.delete-image');
Route::post('gifts/images/{image}/set-featured', [GiftController::class, 'setFeaturedImage'])->name('gifts.set-featured-image');
Route::post('gifts/images/{image}/unset-featured', [GiftController::class, 'unsetFeaturedImage'])->name('gifts.unset-featured-image');
Route::resource('gifts', GiftController::class);

/* Banner Management Routes */
Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
Route::resource('banners', BannerController::class);

/** Payment settings routes */
Route::get('payment-settings', [PaymentSettingController::class, 'index'])->name('payment-settings.index');
Route::put('paystack-setting/{id}', [PaystackSettingController::class, 'update'])->name('paystack-setting.update');
Route::put('localbank-setting/{id}', [LocalBankSettingController::class, 'update'])->name('localbank-setting.update');
