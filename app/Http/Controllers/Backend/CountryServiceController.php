<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Service;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryServiceController extends Controller
{
    private $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
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
     * Get pricing data for a specific country.
     */
    public function getCountryPrices($countryId)
    {
        $country = Country::findOrFail($countryId);
        $services = Service::where('status', 'active')->get();
        
        $pricingData = [];
        
        foreach ($services as $service) {
            $pivotData = $service->countries()->where('country_id', $countryId)->first();
            $currentPrice = $this->pricingService->getServicePrice($service->id, $countryId);
            
            $pricingData[] = [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'service_code' => $service->code,
                'base_price' => $service->price,
                'current_price' => $currentPrice,
                'custom_price' => $pivotData ? $pivotData->pivot->price : null,
                'is_active' => $pivotData ? $pivotData->pivot->is_active : false,
                'has_custom_pricing' => $pivotData !== null,
                'allow_refunds' => $service->allow_refunds
            ];
        }
        
        return response()->json([
            'country' => $country,
            'pricing_data' => $pricingData
        ]);
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

        // Convert is_active to boolean
        $isActive = $request->has('is_active') ? 
            filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

        $success = $this->pricingService->updateCountryServicePrice(
            $request->service_id,
            $request->country_id,
            $request->price,
            $isActive
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update price'
        ], 500);
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

        $successCount = 0;
        $totalCount = count($request->prices);

        foreach ($request->prices as $priceData) {
            // Convert is_active to boolean
            $isActive = isset($priceData['is_active']) ? 
                filter_var($priceData['is_active'], FILTER_VALIDATE_BOOLEAN) : true;

            $success = $this->pricingService->updateCountryServicePrice(
                $priceData['service_id'],
                $request->country_id,
                $priceData['price'],
                $isActive
            );

            if ($success) {
                $successCount++;
            }
        }

        return response()->json([
            'success' => $successCount === $totalCount,
            'message' => "Updated {$successCount} out of {$totalCount} prices",
            'success_count' => $successCount,
            'total_count' => $totalCount
        ]);
    }

    /**
     * Sync prices from API for a country.
     */
    public function syncApiPrices($countryId)
    {
        $country = Country::findOrFail($countryId);
        $services = Service::where('status', 'active')->get();
        
        $syncedCount = 0;
        $errors = [];
        
        foreach ($services as $service) {
            try {
                // This will fetch from API and apply markup
                $price = $this->pricingService->getServicePrice($service->id, $countryId);
                
                if ($price !== null) {
                    $this->pricingService->updateCountryServicePrice(
                        $service->id,
                        $countryId,
                        $price,
                        true
                    );
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to sync {$service->name}: {$e->getMessage()}";
            }
        }
        
        return response()->json([
            'success' => $syncedCount > 0,
            'message' => "Synced {$syncedCount} services",
            'synced_count' => $syncedCount,
            'errors' => $errors
        ]);
    }
}