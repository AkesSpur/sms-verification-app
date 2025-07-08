<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\GeneralSetting;
use App\Models\Service;
use App\Services\PricingService;
use App\Services\SmsPoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CountryServiceController extends Controller
{
    private $pricingService;
    private $smsPoolService;

    public function __construct(PricingService $pricingService, SmsPoolService $smsPoolService)
    {
        $this->pricingService = $pricingService;
        $this->smsPoolService = $smsPoolService;
    }

    /**
     * Display country-service pricing management page.
     */
    public function index()
    {
        $countries = Country::all();
        $services = Service::where('status', 'active')->get();
        
        return view('admin.country-service.index', compact('countries', 'services'));
    }

    /**
     * Get pricing data for a specific country (database only, no API calls)
     */
    public function getCountryPrices($countryId)
    {
        try {
            $country = Country::findOrFail($countryId);
            $services = Service::where('status', 'active')->orderBy('name')->get();
            
            $pricingData = [];
            
            Log::info('Loading country prices from database only', [
                'country_id' => $countryId,
                'country_code' => $country->code
            ]);
            
            foreach ($services as $service) {
                $pivotData = $service->countries()->where('country_id', $countryId)->first();
                
                // Only use database data, no API calls
                $currentPrice = null;
                $apiPriceUsd = null;
                
                if ($pivotData && $pivotData->pivot) {
                    $currentPrice = $pivotData->pivot->final_price ?? $pivotData->pivot->price;
                    $apiPriceUsd = $pivotData->pivot->api_price_usd ?? null;
                } else {
                    // No pricing data available - will show empty
                    $currentPrice = null;
                }
                
                $pricingData[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_code' => $service->code,
                    'base_price' => $service->base_price ?? 0,
                    'current_price' => $currentPrice,
                    'custom_price' => $pivotData ? ($pivotData->pivot->final_price ?? $pivotData->pivot->price) : null,
                    'is_active' => $pivotData ? $pivotData->pivot->is_active : false,
                    'has_custom_pricing' => $pivotData !== null,
                    'allow_refunds' => $service->allow_refunds ?? false,
                    'api_price_usd' => $apiPriceUsd,
                    'api_price' => $pivotData ? $pivotData->pivot->api_price : null,
                    'markup_percentage' => $pivotData ? $pivotData->pivot->markup_percentage : $service->markup_percentage,
                    'final_price' => $pivotData ? $pivotData->pivot->final_price : null,
                    'last_price_update' => $pivotData ? $pivotData->pivot->last_price_update : null,
                    'status' => $pivotData ? $pivotData->pivot->status : 'inactive'
                ];
            }
            
            return response()->json([
                'success' => true,
                'country' => $country,
                'pricing_data' => $pricingData
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get country prices', [
                'country_id' => $countryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pricing data: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Update or create custom pricing for a country-service combination.
     */
    public function updatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|in:true,false,1,0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Convert is_active to boolean
            $isActive = $request->has('is_active') ? 
                filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

            $service = Service::findOrFail($request->service_id);
            $country = Country::findOrFail($request->country_id);

            // Update or create pivot entry with comprehensive data
            $service->countries()->syncWithoutDetaching([
                $request->country_id => [
                    'price' => $request->price,
                    'final_price' => $request->price,
                    'is_active' => $isActive,
                    'status' => $isActive ? 'active' : 'inactive',
                    'last_price_update' => now(),
                    'updated_at' => now()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update price', [
                'country_id' => $request->country_id,
                'service_id' => $request->service_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove custom pricing (revert to API pricing).
     */
    public function removeCustomPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'service_id' => 'required|exists:services,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::findOrFail($request->service_id);
        $service->countries()->detach($request->country_id);

        return response()->json([
            'success' => true,
            'message' => 'Custom pricing removed successfully'
        ]);
    }

    /**
     * Bulk update prices for a country.
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'prices' => 'required|array',
            'prices.*.service_id' => 'required|exists:services,id',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.is_active' => 'sometimes|in:true,false,1,0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $successCount = 0;
            $totalCount = count($request->prices);
            $errors = [];

            foreach ($request->prices as $priceData) {
                try {
                    // Convert is_active to boolean
                    $isActive = isset($priceData['is_active']) ? 
                        filter_var($priceData['is_active'], FILTER_VALIDATE_BOOLEAN) : true;

                    $service = Service::findOrFail($priceData['service_id']);
                    
                    // Update pivot entry with comprehensive data
                    $service->countries()->syncWithoutDetaching([
                        $request->country_id => [
                            'price' => $priceData['price'],
                            'final_price' => $priceData['price'],
                            'is_active' => $isActive,
                            'status' => $isActive ? 'active' : 'inactive',
                            'last_price_update' => now(),
                            'updated_at' => now()
                        ]
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update service {$priceData['service_id']}: {$e->getMessage()}";
                    Log::warning('Bulk update failed for service', [
                        'service_id' => $priceData['service_id'],
                        'country_id' => $request->country_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => $successCount === $totalCount,
                'message' => "Updated {$successCount} out of {$totalCount} prices",
                'success_count' => $successCount,
                'total_count' => $totalCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk update failed', [
                'country_id' => $request->country_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync API prices for all services in a country.
     */
    // public function syncApiPrices(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'country_id' => 'required|exists:countries,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         $country = Country::findOrFail($request->country_id);
    //         $services = Service::where('status', 'active')->get();
            
    //         $successCount = 0;
    //         $totalCount = $services->count();
    //         $errors = [];

    //         // Fetch all API prices in a single call to avoid timeout
    //         $allApiPrices = $this->fetchAllApiPrices($country->code);
            
    //         if (empty($allApiPrices)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Failed to fetch API prices from SMSPool',
    //                 'errors' => ['No API response received']
    //             ], 500);
    //         }

    //         $generalSettings = GeneralSetting::first();
    //         $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
    //         $defaultMarkupPercentage = $generalSettings->api_price_markup_percentage ?? 20.00;

    //         foreach ($services as $service) {
    //             try {
    //                 // Get API price from bulk data
    //                 if (isset($allApiPrices[$service->code]['cost'])) {
    //                     $apiPriceUsd = (float) $allApiPrices[$service->code]['cost'];
    //                     $markupPercentage = $defaultMarkupPercentage;
    //                     // $markupPercentage = $service->markup_percentage ?? $defaultMarkupPercentage;
                        
    //                     // Calculate final price with markup and convert to Naira
    //                     $priceWithMarkup = $apiPriceUsd * (1 + ($markupPercentage / 100));
    //                     $finalPriceNaira = $priceWithMarkup * $exchangeRate;
    //                     $finalPriceNaira = ceil($finalPriceNaira / 10) * 10; // Round to next 10th
                        
    //                     // Update pivot entry directly
    //                     $service->countries()->syncWithoutDetaching([
    //                         $country->id => [
    //                             'api_price' => $apiPriceUsd,
    //                             'price' => $finalPriceNaira,
    //                             'final_price' => $finalPriceNaira,
    //                             'markup_percentage' => $markupPercentage,
    //                             'is_active' => true,
    //                             'status' => 'active',
    //                             'last_price_update' => now(),
    //                             'updated_at' => now()
    //                         ]
    //                     ]);
                        
    //                     $successCount++;
                        
    //                     Log::info('API price synced', [
    //                         'service' => $service->name,
    //                         'country' => $country->name,
    //                         'api_price_usd' => $apiPriceUsd,
    //                         'final_price_naira' => $finalPriceNaira,
    //                         'markup_percentage' => $markupPercentage
    //                     ]);
    //                 } else {
    //                     $errors[] = "No API price found for service: {$service->name} (code: {$service->code})";
    //                     Log::warning('No API price found in bulk data', [
    //                         'service' => $service->name,
    //                         'service_code' => $service->code,
    //                         'country' => $country->name
    //                     ]);
    //                 }
    //             } catch (\Exception $e) {
    //                 $errors[] = "Error syncing {$service->name}: {$e->getMessage()}";
    //                 Log::error('API sync failed for service', [
    //                     'service' => $service->name,
    //                     'country' => $country->name,
    //                     'error' => $e->getMessage()
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'success' => $successCount === $totalCount,
    //             'message' => "Synced {$successCount} out of {$totalCount} services",
    //             'success_count' => $successCount,
    //             'total_count' => $totalCount,
    //             'errors' => $errors
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('API sync failed', [
    //             'country_id' => $request->country_id,
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'API sync failed: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}