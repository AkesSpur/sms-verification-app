<?php
use App\Http\Controllers\Backend\DaisyOrderController;
use App\Http\Controllers\Backend\DaisyServiceController;
use App\Http\Controllers\Backend\GiftOrderController;
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
use App\Http\Controllers\Backend\SmsOrderController;
use App\Http\Controllers\Backend\SocialMediaCategoryController;
use App\Http\Controllers\Backend\SocialMediaProductController;
use App\Http\Controllers\Backend\SocialMediaOrderController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Backend\ResellerProductController;
use App\Http\Controllers\Backend\ResellerProductLogController;
use App\Http\Controllers\Backend\ResellerRequestController;
use App\Http\Controllers\Backend\ResellerOrderAdminController;
use App\Http\Controllers\Backend\VirtualAccountController;
use Illuminate\Support\Facades\Route;


/* Dashboard route */
Route::get('/', [AdminController::class, 'index'])->name('dashboard');

/* Admin Profile routes */
Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');


/** customer list routes */
Route::put('customer/status-change', [CustomerListController::class, 'changeStatus'])->name('customer.status-change');
Route::put('customer-list/{id}/update-email', [CustomerListController::class, 'updateEmail'])->name('customer-list.update-email');
Route::get('customer', [CustomerListController::class, 'index'])->name('customer.index');
Route::post('customers/{id}/verify-email', [CustomerListController::class, 'verifyEmail'])->name('customers.verify-email');
Route::post('customers/{id}/send-reset-link', [CustomerListController::class, 'sendResetLink'])->name('customers.send-reset-link');
Route::delete('customers/{id}', [CustomerListController::class, 'destroy'])->name('customers.destroy');
// Customer reseller management
Route::post('customers/{user}/make-reseller', [CustomerListController::class, 'makeReseller'])->name('customers.make-reseller');
Route::post('customers/{user}/remove-reseller', [CustomerListController::class, 'removeReseller'])->name('customers.remove-reseller');
Route::get('resellers', [CustomerListController::class, 'resellers'])->name('resellers.index');
Route::get('admin-list', [AdminListController::class, 'index'])->name('admin-list.index');
Route::put('admin-list/status-change', [AdminListController::class, 'changeStatus'])->name('admin-list.status-change');
Route::put('admin-list/{id}/update-email', [AdminListController::class, 'updateEmail'])->name('admin-list.update-email');
Route::delete('admin-list/{id}', [AdminListController::class, 'destroy'])->name('admin-list.destroy');
Route::post('admins/{id}/verify-email', [AdminListController::class, 'verifyEmail'])->name('admins.verify-email');
Route::post('admins/{id}/send-reset-link', [AdminListController::class, 'sendResetLink'])->name('admins.send-reset-link');
// Admin reseller management
Route::post('admins/{user}/make-reseller', [AdminListController::class, 'makeReseller'])->name('admins.make-reseller');
Route::post('admins/{user}/remove-reseller', [AdminListController::class, 'removeReseller'])->name('admins.remove-reseller');


/** Add and withdraw funds routes */
Route::get('add-fund/{id}', [FundManagementController::class, 'addIndex'])->name('add-fund.index');
Route::post('fund-user/{id}', [FundManagementController::class, 'addFund'])->name('fund-user');
Route::get('withdraw-fund/{id}', [FundManagementController::class, 'withdrawIndex'])->name('withdraw-fund.index');
Route::post('withdraw-user-fund/{id}', [FundManagementController::class, 'withdrawFund'])->name('withdraw-user-fund');

/** Transaction management routes */
Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('transactions/data', [TransactionController::class, 'getData'])->name('transactions.data');
Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
Route::get('transactions/export/csv', [TransactionController::class, 'export'])->name('transactions.export');

/** Virtual Accounts listing */
Route::get('virtual-accounts', [VirtualAccountController::class, 'index'])->name('virtual-accounts.index');

/** manage user routes */
Route::get('manage-user', [ManageUserController::class, 'index'])->name('manage-user.index');
Route::post('manage-user', [ManageUserController::class, 'create'])->name('manage-user.create');


/* Settings Routes */
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('general-setting-update', [SettingController::class, 'updateGeneralSetting'])->name('general-setting-update');
Route::put('email-setting-update', [SettingController::class, 'updateEmailSetting'])->name('email-setting-update');
Route::put('logo-setting-update', [SettingController::class, 'updateLogoSetting'])->name('logo-setting-update');
Route::post('update-exchange-rate', [SettingController::class, 'updateExchangeRate'])->name('update-exchange-rate');

/* Country Service Pricing Routes */
Route::get('country-service', [CountryServiceController::class, 'index'])->name('country-service.index');
// Route::get('country-service/{country}/prices', [CountryServiceController::class, 'getCountryPrices'])->name('country-service.prices');
Route::post('country-service/get-country-prices/{country}', [CountryServiceController::class, 'getCountryPrices'])->name('country-service.get-prices');
Route::post('country-service/update-price', [CountryServiceController::class, 'updatePrice'])->name('country-service.update-price');
Route::delete('country-service/remove-price', [CountryServiceController::class, 'removeCustomPrice'])->name('country-service.remove-price');
Route::post('country-service/bulk-update', [CountryServiceController::class, 'bulkUpdatePrices'])->name('country-service.bulk-update');

// Route::post('country-service/{country}/sync-api', [CountryServiceController::class, 'syncApiPrices'])->name('country-service.sync-api');

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

/* Gift Order Management Routes */
Route::get('gift-orders/export', [GiftOrderController::class, 'export'])->name('gift-orders.export');
Route::put('gift-orders/{giftOrder}/status', [GiftOrderController::class, 'updateStatus'])->name('gift-orders.update-status');
Route::resource('gift-orders', GiftOrderController::class)->only(['index', 'show', 'destroy']);

/* Banner Management Routes */
Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
Route::resource('banners', BannerController::class);

/* SMS Order Management Routes */
Route::get('sms-orders/statistics', [SmsOrderController::class, 'statistics'])->name('sms-orders.statistics');
Route::get('sms-orders/export', [SmsOrderController::class, 'export'])->name('sms-orders.export');
Route::post('sms-orders/bulk-action', [SmsOrderController::class, 'bulkAction'])->name('sms-orders.bulk-action');
Route::resource('sms-orders', SmsOrderController::class)->only(['index', 'show', 'destroy'])->parameter('sms-orders', 'order');
Route::post('sms-orders/{order}/set-sms-code', [SmsOrderController::class, 'setSmsCode'])->name('sms-orders.set-sms-code');
Route::post('sms-orders/{order}/retry-sms', [SmsOrderController::class, 'retrySms'])->name('sms-orders.retry-sms');
Route::post('sms-orders/{order}/force-cancel', [SmsOrderController::class, 'forceCancel'])->name('sms-orders.force-cancel');
Route::post('sms-orders/{order}/mark-review', [SmsOrderController::class, 'markForReview'])->name('sms-orders.mark-review');
Route::post('sms-orders/{order}/remove-review', [SmsOrderController::class, 'removeFromReview'])->name('sms-orders.remove-review');

/** DaisyService Management Routes */
Route::prefix('daisy-services')->name('daisy-services.')->group(function () {
    Route::get('/', [DaisyServiceController::class, 'index'])->name('index');
    Route::get('/create', [DaisyServiceController::class, 'create'])->name('create');
    Route::post('/', [DaisyServiceController::class, 'store'])->name('store');
    Route::get('/{daisyService}', [DaisyServiceController::class, 'show'])->name('show');
    Route::get('/{daisyService}/edit', [DaisyServiceController::class, 'edit'])->name('edit');
    Route::put('/{daisyService}', [DaisyServiceController::class, 'update'])->name('update');
    Route::delete('/{daisyService}', [DaisyServiceController::class, 'destroy'])->name('destroy');
    Route::get('/{daisyService}/toggle-status', [DaisyServiceController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{daisyService}/toggle-popular', [DaisyServiceController::class, 'togglePopular'])->name('toggle-popular');
    Route::get('/{daisyService}/manage-prices', [DaisyServiceController::class, 'managePrices'])->name('manage-prices');
    Route::put('/{daisyService}/update-prices', [DaisyServiceController::class, 'updatePrices'])->name('update-prices');
    Route::post('/{daisyService}/sync-prices', [DaisyServiceController::class, 'syncPricesFromApi'])->name('sync-prices');
    Route::post('/{daisyService}/bulk-update-prices', [DaisyServiceController::class, 'bulkUpdatePrices'])->name('bulk-update-prices');
    Route::post('/{daisyService}/update-price', [DaisyServiceController::class, 'updatePrice'])->name('update-price');
    Route::post('/{daisyService}/toggle-price-status', [DaisyServiceController::class, 'togglePriceStatus'])->name('toggle-price-status');
    Route::delete('/price/{price}', [DaisyServiceController::class, 'deletePrice'])->name('delete-price');
    Route::post('/bulk-action', [DaisyServiceController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/api/statistics', [DaisyServiceController::class, 'getStatistics'])->name('statistics');
    Route::post('/bulk-sync-prices', [DaisyServiceController::class, 'bulkSyncPrices'])->name('bulk-sync-prices');
    

});

   /** Daisy Order Management Routes */
Route::prefix('daisy-orders')->name('daisy-orders.')->group(function () {
    Route::get('/', [DaisyOrderController::class, 'index'])->name('index');
    Route::get('/{daisyOrder}', [DaisyOrderController::class, 'show'])->name('show');
    Route::post('/{daisyOrder}/update-status', [DaisyOrderController::class, 'updateStatus'])->name('update-status');
    Route::post('/{daisyOrder}/refresh-sms', [DaisyOrderController::class, 'refreshSmsStatus'])->name('refresh-sms');
    Route::post('/{daisyOrder}/cancel', [DaisyOrderController::class, 'cancelOrder'])->name('cancel');
    Route::post('/bulk-update', [DaisyOrderController::class, 'bulkUpdate'])->name('bulk-update');
    Route::get('/export/csv', [DaisyOrderController::class, 'export'])->name('export');
});

/* Social Media Boosting Management Routes */
Route::resource('social-media-categories', SocialMediaCategoryController::class);
Route::get('social-media-products/by-category/{category}', [SocialMediaProductController::class, 'getByCategory'])->name('social-media-products.by-category');
Route::post('social-media-products/sync-owlet-services', [SocialMediaProductController::class, 'syncOwletServices'])->name('social-media-products.sync-owlet-services');
Route::post('social-media-products/test-owlet-connection', [SocialMediaProductController::class, 'testOwletConnection'])->name('social-media-products.test-owlet-connection');
Route::post('social-media-products/bulk-update-prices', [SocialMediaProductController::class, 'bulkUpdatePrices'])->name('social-media-products.bulk-update-prices');
Route::post('social-media-products/bulk-update-status', [SocialMediaProductController::class, 'bulkUpdateStatus'])->name('social-media-products.bulk-update-status');
Route::resource('social-media-products', SocialMediaProductController::class);
Route::get('social-media-orders/export', [SocialMediaOrderController::class, 'export'])->name('social-media-orders.export');
Route::put('social-media-orders/{socialMediaOrder}/status', [SocialMediaOrderController::class, 'updateStatus'])->name('social-media-orders.update-status');
Route::post('social-media-orders/bulk-update-status', [SocialMediaOrderController::class, 'bulkUpdateStatus'])->name('social-media-orders.bulk-update-status');
Route::put('social-media-orders/{socialMediaOrder}/update-status', [SocialMediaOrderController::class, 'updateStatus'])->name('social-media-orders.update-status');
Route::resource('social-media-orders', SocialMediaOrderController::class)->only(['index', 'show']);

/** Payment settings routes */
Route::get('payment-settings', [PaymentSettingController::class, 'index'])->name('payment-settings.index');
Route::put('paystack-setting/{id}', [PaystackSettingController::class, 'update'])->name('paystack-setting.update');
Route::put('localbank-setting/{id}', [LocalBankSettingController::class, 'update'])->name('localbank-setting.update');

/* Reseller Products Management Routes */
Route::resource('reseller-products', ResellerProductController::class);

/* Reseller Product Log Management Routes */
Route::get('reseller-product-logs/by-product/{product}', [ResellerProductLogController::class, 'getByProduct'])->name('reseller-product-logs.by-product');
Route::get('reseller-product-logs/add-logs', [ResellerProductLogController::class, 'showAddLogsForm'])->name('reseller-product-logs.add-logs');
Route::post('reseller-product-logs/add-logs', [ResellerProductLogController::class, 'addLogs'])->name('reseller-product-logs.store-multiple');
Route::resource('reseller-product-logs', ResellerProductLogController::class);

/* Reseller Requests Management Routes */
Route::get('reseller-requests', [ResellerRequestController::class, 'index'])->name('reseller-requests.index');
Route::post('reseller-requests/{resellerRequest}/approve', [ResellerRequestController::class, 'approve'])->name('reseller-requests.approve');
Route::post('reseller-requests/{resellerRequest}/reject', [ResellerRequestController::class, 'reject'])->name('reseller-requests.reject');

Route::get('reseller-orders', [ResellerOrderAdminController::class, 'index'])->name('reseller-orders.index');
Route::get('reseller-orders/{order}', [ResellerOrderAdminController::class, 'show'])->name('reseller-orders.show');
Route::post('reseller-requests/{resellerRequest}/approve', [ResellerRequestController::class, 'approve'])->name('reseller-requests.approve');
Route::post('reseller-requests/{resellerRequest}/reject', [ResellerRequestController::class, 'reject'])->name('reseller-requests.reject');
