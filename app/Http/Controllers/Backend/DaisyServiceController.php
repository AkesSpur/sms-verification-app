<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DaisyService;
use App\Models\DaisyServicePrice;
use App\Models\GeneralSetting;
use App\Services\DaisySmsService;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class DaisyServiceController extends Controller
{
    protected $daisySmsService;
    protected $exchangeRateService;

    public function __construct(DaisySmsService $daisySmsService, ExchangeRateService $exchangeRateService)
    {
        $this->daisySmsService = $daisySmsService;
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        $query = DaisyService::with(['servicePrices' => function($q) {
            $q->where('status', true)->orderBy('price_naira', 'asc');
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Popular filter
        if ($request->filled('popular')) {
            $query->where('is_popular', $request->popular);
        }

        $services = $query->ordered()->paginate(50);
        
        // Get statistics
        $stats = [
            'total_services' => DaisyService::count(),
            'active_services' => DaisyService::active()->count(),
            'popular_services' => DaisyService::popular()->count(),
            'total_prices' => DaisyServicePrice::count(),
            'active_prices' => DaisyServicePrice::active()->count()
        ];

        return view('admin.daisy-services.index', compact('services', 'stats'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.daisy-services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:daisy_services,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_popular' => 'required|boolean',
            'meta_data' => 'nullable|array'
        ]);

        $data = $request->all();
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['meta_data'] = $request->meta_data ?? [];

        DaisyService::create($data);

        toastr('Service created successfully!', 'success');
        return redirect()->route('admin.daisy-services.index');
    }

    /**
     * Display the specified service.
     */
    public function show(DaisyService $daisyService)
    {
        $daisyService->load(['servicePrices' => function($q) {
            $q->orderBy('price_naira', 'asc');
        }]);
        
        $statistics = $daisyService->getStatistics();
        
        return view('admin.daisy-services.show', compact('daisyService', 'statistics'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(DaisyService $daisyService)
    {
        return view('admin.daisy-services.edit', compact('daisyService'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, DaisyService $daisyService)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:daisy_services,code,' . $daisyService->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_popular' => 'required|boolean',
            'meta_data' => 'nullable|array'
        ]);

        $data = $request->all();
        $data['sort_order'] = $request->sort_order ?? $daisyService->sort_order;
        $data['meta_data'] = $request->meta_data ?? [];

        $daisyService->update($data);

        toastr('Service updated successfully!', 'success');
        return redirect()->route('admin.daisy-services.index');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(DaisyService $daisyService)
    {
        try {
            // Check if service has active orders
            $activeOrdersCount = $daisyService->daisyOrders()->whereIn('status', ['active', 'pending'])->count();
            
            if ($activeOrdersCount > 0) {
                return response([
                    'status' => 'error',
                    'message' => 'Cannot delete service with active orders. Please complete or cancel active orders first.'
                ], 400);
            }

            $daisyService->delete();

            return response([
                'status' => 'success',
                'message' => 'Service deleted successfully!'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting DaisyService: ' . $e->getMessage());
            return response([
                'status' => 'error',
                'message' => 'An error occurred while deleting the service.'
            ], 500);
        }
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(DaisyService $daisyService)
    {
        $daisyService->update(['status' => !$daisyService->status]);
        
        $status = $daisyService->status ? 'activated' : 'deactivated';
        toastr("Service {$status} successfully!", 'success');
        
        return redirect()->route('admin.daisy-services.index');
    }

    /**
     * Toggle popular status
     */
    public function togglePopular(DaisyService $daisyService)
    {
        $daisyService->update(['is_popular' => !$daisyService->is_popular]);
        
        $status = $daisyService->is_popular ? 'marked as popular' : 'removed from popular';
        toastr("Service {$status} successfully!", 'success');
        
        return redirect()->route('admin.daisy-services.index');
    }

    /**
     * Show price management for a service
     */
    public function managePrices(DaisyService $daisyService)
    {
        $daisyService->load(['servicePrices' => function($q) {
            $q->orderBy('country_name', 'asc');
        }]);
        
        $prices = $daisyService->servicePrices;
        $generalSettings = GeneralSetting::first();
        $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
        
        return view('admin.daisy-services.manage-prices', compact('daisyService', 'prices', 'exchangeRate'));
    }

    /**
     * Update service prices
     */
    public function updatePrices(Request $request, DaisyService $daisyService)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.price_usd' => 'required|numeric|min:0',
            'prices.*.price_naira' => 'nullable|numeric|min:0',
            'prices.*.markup_percentage' => 'nullable|numeric|min:0|max:100',
            'prices.*.status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $generalSettings = GeneralSetting::first();
            $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
            $updatedCount = 0;

            foreach ($request->prices as $priceId => $priceData) {
                $servicePrice = DaisyServicePrice::find($priceId);
                if ($servicePrice && $servicePrice->service_id == $daisyService->id) {
                    $nairaPrice = $priceData['price_naira'] ?? ($priceData['price_usd'] * $exchangeRate);
                    
                    $servicePrice->update([
                        'price_usd' => $priceData['price_usd'],
                        'price_naira' => round($nairaPrice, 2),
                        'markup_percentage' => $priceData['markup_percentage'] ?? 0,
                        'status' => $priceData['status']
                    ]);
                    $updatedCount++;
                }
            }

            DB::commit();
            
            toastr("Updated {$updatedCount} price(s) successfully!", 'success');
            return redirect()->route('admin.daisy-services.manage-prices', $daisyService);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating service prices: ' . $e->getMessage());
            toastr('An error occurred while updating prices.', 'error');
            return redirect()->back();
        }
    }



    /**
     * Sync prices from API (US only)
     */
    public function syncPricesFromApi(DaisyService $daisyService)
    {
        try {
            // Get general settings for exchange rate
            $generalSettings = GeneralSetting::first();
            $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
            
            // Initialize DaisySMS service
            $daisySmsService = new DaisySmsService();
            
            // Fetch real prices from DaisySMS API for all countries
            $pricesResponse = $daisySmsService->getServicePrices($daisyService->code);
            
            if (!$pricesResponse['success']) {
                Log::error('Failed to fetch prices from DaisySMS API', [
                    'service_code' => $daisyService->code,
                    'error' => $pricesResponse['error']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch prices from API: ' . $pricesResponse['error']
                ]);
            }
            
            $apiPrices = $pricesResponse['prices'];
            

            
            if (empty($apiPrices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No prices available for this service from the API'
                ]);
            }
            
            // Filter to only US prices since we only sell US numbers
            // Based on API analysis, US prices come with country code '187'
            $usPrices = [];
            foreach ($apiPrices as $countryCode => $priceInfo) {
                // Check for US country codes - DaisySMS uses '187' for US
                if ($countryCode == '187' || $countryCode === 'us' || $countryCode === 'US' || $countryCode === '1') {
                    // Normalize the price structure and country code
                    if (is_array($priceInfo) && isset($priceInfo['price_usd'], $priceInfo['available'])) {
                        $usPrices['us'] = [
                            'price_usd' => $priceInfo['price_usd'],
                            'available' => $priceInfo['available']
                        ];
                    } else {
                        // Fallback for different price structures
                        $usPrices['us'] = $priceInfo;
                    }
                    break; // Only need one US price
                }
            }
            
            if (empty($usPrices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No US prices available for this service from the API'
                ]);
            }
            
            // Sync only the US prices
            $syncedCount = DaisyServicePrice::syncFromApiData($daisyService->id, $usPrices, $exchangeRate);
            
            // Clear cache
            Cache::forget('daisy_sms_services');
            
            Log::info('Successfully synced prices from DaisySMS API', [
                'service_id' => $daisyService->id,
                'service_code' => $daisyService->code,
                'synced_count' => $syncedCount,
                'api_prices' => $apiPrices
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Synced {$syncedCount} price(s) from DaisySMS API successfully!"
            ]);
            
        } catch (Exception $e) {
            Log::error('Error syncing prices from API', [
                'service_id' => $daisyService->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while syncing prices from API: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk actions for services
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,mark_popular,unmark_popular',
            'services' => 'required|array',
            'services.*' => 'exists:daisy_services,id'
        ]);

        try {
            $services = DaisyService::whereIn('id', $request->services);
            $count = $services->count();
            
            switch ($request->action) {
                case 'activate':
                    $services->update(['status' => true]);
                    $message = "Activated {$count} service(s) successfully!";
                    break;
                    
                case 'deactivate':
                    $services->update(['status' => false]);
                    $message = "Deactivated {$count} service(s) successfully!";
                    break;
                    
                case 'mark_popular':
                    $services->update(['is_popular' => true]);
                    $message = "Marked {$count} service(s) as popular successfully!";
                    break;
                    
                case 'unmark_popular':
                    $services->update(['is_popular' => false]);
                    $message = "Removed {$count} service(s) from popular successfully!";
                    break;
                    
                case 'delete':
                    // Check for active orders
                    $servicesWithActiveOrders = $services->whereHas('daisyOrders', function($q) {
                        $q->whereIn('status', ['active', 'pending']);
                    })->count();
                    
                    if ($servicesWithActiveOrders > 0) {
                        toastr('Cannot delete services with active orders.', 'error');
                        return redirect()->back();
                    }
                    
                    $services->delete();
                    $message = "Deleted {$count} service(s) successfully!";
                    break;
            }
            
            toastr($message, 'success');
            return redirect()->route('admin.daisy-services.index');
            
        } catch (Exception $e) {
            Log::error('Error performing bulk action: ' . $e->getMessage());
            toastr('An error occurred while performing the bulk action.', 'error');
            return redirect()->back();
        }
    }

    /**
     * Get service statistics for dashboard
     */
    public function getStatistics()
    {
        $stats = [
            'total_services' => DaisyService::count(),
            'active_services' => DaisyService::active()->count(),
            'popular_services' => DaisyService::popular()->count(),
            'total_prices' => DaisyServicePrice::count(),
            'active_prices' => DaisyServicePrice::active()->count(),
            'countries_count' => DaisyServicePrice::distinct('country_code')->count(),
            'average_price_naira' => DaisyServicePrice::active()->avg('price_naira'),
            'cheapest_price' => DaisyServicePrice::active()->min('price_naira'),
            'most_expensive_price' => DaisyServicePrice::active()->max('price_naira')
        ];
        
        return response()->json($stats);
    }

    /**
     * Update individual price via AJAX
     */
    public function updatePrice(Request $request, DaisyService $daisyService)
    {
        $request->validate([
            'price_id' => 'required|exists:daisy_service_prices,id',
            'original_price_usd' => 'nullable|numeric|min:0',
            'price_naira' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|boolean'
        ]);

        // Find the price record
        $price = DaisyServicePrice::findOrFail($request->price_id);
        
        // Ensure the price belongs to the service
        if ($price->service_id !== $daisyService->id) {
            return response()->json([
                'success' => false,
                'message' => 'Price does not belong to this service.'
            ], 422);
        }

        // Ensure at least one price field is provided
        if (!$request->has('original_price_usd') && !$request->has('price_naira')) {
            return response()->json([
                'success' => false,
                'message' => 'Either USD price or Naira price must be provided.'
            ], 422);
        }

        try {
            $generalSettings = GeneralSetting::first();
            $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
            $markup = $generalSettings->api_price_markup_percentage ?? 0;
            
            $markupPercentage = $request->markup_percentage ?? $markup;
            
            // Determine which price to use as base
            if ($request->has('price_naira') && $request->price_naira > 0) {
                // Direct Naira price update
                $finalPriceNaira = $request->price_naira;
                $finalPriceUsd = round($finalPriceNaira / $exchangeRate, 4);
                $originalPriceUsd = round($finalPriceUsd / (1 + $markupPercentage / 100), 4);
            } else {
                // USD price update with correct calculation
                $originalPriceUsd = $request->original_price_usd;
                // Convert USD to Naira first, then apply markup percentage
                $baseNairaPrice = $originalPriceUsd * $exchangeRate;
                $finalPrice = $baseNairaPrice * (1 + ($markupPercentage / 100));
                // Round to nearest tenth (134 -> 140, 1227 -> 1230)
                $finalPriceNaira = ceil($finalPrice / 10) * 10;
                $finalPriceUsd = round($finalPriceNaira / $exchangeRate, 4);
            }
            
            $price->update([
                'original_price_usd' => round($originalPriceUsd, 4),
                'markup_percentage' => $markupPercentage,
                'price_usd' => round($finalPriceUsd, 4),
                'price_naira' => $finalPriceNaira,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully!',
                'data' => [
                    'original_price_usd' => round($originalPriceUsd, 4),
                    'final_price_usd' => round($finalPriceUsd, 4),
                    'final_price_naira' => $finalPriceNaira,
                    'markup_percentage' => $markupPercentage
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error updating price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price.'
            ], 500);
        }
    }

    /**
     * Bulk update prices
     */
    public function bulkUpdatePrices(Request $request, DaisyService $daisyService)
    {
        $request->validate([
            'action' => 'required|in:update_markup,update_status,multiply_prices,add_fixed_amount',
            'value' => 'required|numeric',
            'selected_prices' => 'nullable|array',
            'selected_prices.*' => 'exists:daisy_service_prices,id'
        ]);

        try {
            DB::beginTransaction();
            
            $query = $daisyService->servicePrices();
            
            // If specific prices are selected, filter by them
            if ($request->selected_prices && count($request->selected_prices) > 0) {
                $query->whereIn('id', $request->selected_prices);
            }
            
            $prices = $query->get();
            $generalSettings = GeneralSetting::first();
            $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
            $updatedCount = 0;

            foreach ($prices as $price) {
                $updated = false;
                
                switch ($request->action) {
                    case 'update_markup':
                        $newMarkup = max(0, min(100, $request->value));
                        $finalPriceUsd = $price->original_price_usd * (1 + $newMarkup / 100);
                        $finalPriceNaira = $finalPriceUsd * $exchangeRate;
                        
                        $price->update([
                            'markup_percentage' => $newMarkup,
                            'price_usd' => round($finalPriceUsd, 4),
                            'price_naira' => round($finalPriceNaira, 2)
                        ]);
                        $updated = true;
                        break;
                        
                    case 'update_status':
                        $price->update(['status' => (bool)$request->value]);
                        $updated = true;
                        break;
                        
                    case 'multiply_prices':
                        if ($request->value > 0) {
                            $newOriginalPrice = $price->original_price_usd * $request->value;
                            $finalPriceUsd = $newOriginalPrice * (1 + $price->markup_percentage / 100);
                            $finalPriceNaira = $finalPriceUsd * $exchangeRate;
                            
                            $price->update([
                                'original_price_usd' => round($newOriginalPrice, 4),
                                'price_usd' => round($finalPriceUsd, 4),
                                'price_naira' => round($finalPriceNaira, 2)
                            ]);
                            $updated = true;
                        }
                        break;
                        
                    case 'add_fixed_amount':
                        $newOriginalPrice = max(0, $price->original_price_usd + $request->value);
                        $finalPriceUsd = $newOriginalPrice * (1 + $price->markup_percentage / 100);
                        $finalPriceNaira = $finalPriceUsd * $exchangeRate;
                        
                        $price->update([
                            'original_price_usd' => round($newOriginalPrice, 4),
                            'price_usd' => round($finalPriceUsd, 4),
                            'price_naira' => round($finalPriceNaira, 2)
                        ]);
                        $updated = true;
                        break;
                }
                
                if ($updated) {
                    $updatedCount++;
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} price(s)!",
                'updated_count' => $updatedCount
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk price update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update prices. Please try again.'
            ], 500);
        }
    }

    /**
     * Toggle price status via AJAX
     */
    public function togglePriceStatus(Request $request, DaisyService $daisyService)
    {
        try {
            $request->validate([
                'price_id' => 'required|exists:daisy_service_prices,id',
                'status' => 'required|boolean'
            ]);

            $price = DaisyServicePrice::findOrFail($request->price_id);
            $price->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Price status updated successfully!',
                'new_status' => $price->status
            ]);
        } catch (Exception $e) {
            Log::error('Error toggling price status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price status.'
            ], 500);
        }
    }

    /**
     * Delete a price via AJAX
     */
    public function deletePrice(DaisyServicePrice $price)
    {
        try {
            $countryName = $price->country_name;
            $price->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Price for {$countryName} deleted successfully!"
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete price.'
            ], 500);
        }
    }

    /**
     * Bulk sync all services prices from DaisySMS API
     * Optimized to make only one API call instead of individual calls per service
     */
    public function bulkSyncPrices()
    {
        try {
            // Get general settings for exchange rate
            $generalSettings = GeneralSetting::first();
            $exchangeRate = $generalSettings->usd_to_ngn_rate ?? 1600;
            
            // Make a single API call to get all service prices
            $allPricesResponse = $this->daisySmsService->getAllServicePrices();
            
            if (!$allPricesResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch prices from API: ' . $allPricesResponse['error']
                ], 400);
            }

            $allServicePrices = $allPricesResponse['prices'];
            
            if (empty($allServicePrices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No service prices found from API'
                ], 400);
            }

            $syncedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $syncResults = [];

            Log::info('Starting optimized bulk price sync', [
                'total_api_services' => count($allServicePrices),
                'single_api_call' => true,
                'timestamp' => now()->toISOString()
            ]);

            // Process each service from the single API response
            foreach ($allServicePrices as $serviceCode => $servicePrices) {
                // Check if service exists in our database
                $daisyService = DaisyService::where('code', $serviceCode)->first();
                
                if (!$daisyService) {
                    // Skip services that don't exist in our database
                    $skippedCount++;
                    $syncResults[] = [
                        'service_code' => $serviceCode,
                        'status' => 'skipped',
                        'reason' => 'Service not found in database'
                    ];
                    continue;
                }

                try {
                    // Filter to only US prices since we only sell US numbers
                    // Based on API analysis, US prices come with country code '187'
                    $usPrices = [];
                    foreach ($servicePrices as $countryCode => $priceInfo) {
                        // Check for US country codes - DaisySMS uses '187' for US
                        if ($countryCode == '187' || $countryCode === 'us' || $countryCode === 'US' || $countryCode === '1') {
                            $usPrices['us'] = [
                                'price_usd' => $priceInfo['price_usd'],
                                'available' => $priceInfo['available']
                            ];
                            break; // Only need one US price
                        }
                    }
                    
                    if (empty($usPrices)) {
                        $errorCount++;
                        $syncResults[] = [
                            'service_code' => $serviceCode,
                            'service_name' => $daisyService->name,
                            'status' => 'error',
                            'reason' => 'No US prices available for this service'
                        ];
                        continue;
                    }
                    
                    // Sync the US prices for this service
                    $syncedPriceCount = DaisyServicePrice::syncFromApiData($daisyService->id, $usPrices, $exchangeRate);
                    
                    if ($syncedPriceCount > 0) {
                        $syncedCount++;
                        $syncResults[] = [
                            'service_code' => $serviceCode,
                            'service_name' => $daisyService->name,
                            'status' => 'synced',
                            'message' => "Synced {$syncedPriceCount} price(s)"
                        ];
                        
                        Log::info('Successfully synced prices from bulk API response', [
                            'service_id' => $daisyService->id,
                            'service_code' => $serviceCode,
                            'synced_count' => $syncedPriceCount,
                            'us_prices' => $usPrices
                        ]);
                    } else {
                        $errorCount++;
                        $syncResults[] = [
                            'service_code' => $serviceCode,
                            'service_name' => $daisyService->name,
                            'status' => 'error',
                            'reason' => 'No prices were synced'
                        ];
                    }
                    
                } catch (Exception $e) {
                    $errorCount++;
                    $syncResults[] = [
                        'service_code' => $serviceCode,
                        'service_name' => $daisyService->name ?? 'Unknown',
                        'status' => 'error',
                        'reason' => $e->getMessage()
                    ];
                    Log::error('Error syncing service prices from bulk response', [
                        'service_code' => $serviceCode,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Clear cache after bulk sync
            Cache::forget('daisy_sms_services');

            Log::info('Optimized bulk price sync completed', [
                'total_services' => count($allServicePrices),
                'synced_count' => $syncedCount,
                'skipped_count' => $skippedCount,
                'error_count' => $errorCount,
                'api_calls_made' => 1,
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Bulk sync completed! Synced: {$syncedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}",
                'summary' => [
                    'total_services' => count($allServicePrices),
                    'synced_count' => $syncedCount,
                    'skipped_count' => $skippedCount,
                    'error_count' => $errorCount,
                    'api_calls_made' => 1
                ],
                'results' => $syncResults
            ]);

        } catch (Exception $e) {
            Log::error('Optimized bulk sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}