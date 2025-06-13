<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Service;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    private $smsActivateService;
    
    public function __construct(SmsActivateService $smsActivateService)
    {
        $this->smsActivateService = $smsActivateService;
    }

    /**
     * Get price for a service in a specific country.
     * Uses pivot table first, then API with markup.
     */
    public function getServicePrice($serviceId, $countryId)
    {
        $service = Service::find($serviceId);
        $country = Country::find($countryId);
        
        if (!$service || !$country) {
            return null;
        }

        // Check if price exists in pivot table
        $pivotPrice = $this->getPivotTablePrice($service, $countryId);
        if ($pivotPrice !== null) {
            return $pivotPrice;
        }

        // Fetch from API and apply markup
        return $this->getApiPriceWithMarkup($service, $country);
    }

    /**
     * Get price from pivot table if exists and is active.
     */
    private function getPivotTablePrice($service, $countryId)
    {
        $countryService = $service->countries()->where('country_id', $countryId)->first();
        
        if ($countryService && $countryService->pivot->is_active) {
            return $countryService->pivot->price;
        }
        
        return null;
    }

    /**
     * Fetch price from SMS Activate API and apply markup.
     */
    private function getApiPriceWithMarkup($service, $country)
    {
        $generalSettings = GeneralSetting::first();
        
        if (!$generalSettings || !$generalSettings->enable_dynamic_pricing) {
            return $service->price; // Return base price if dynamic pricing is disabled
        }

        // Cache key for API price
        $cacheKey = "api_price_{$service->code}_{$country->code}";
        
        // Try to get cached price (cache for 30 minutes)
        $apiPrice = Cache::remember($cacheKey, 1800, function () use ($service, $country) {
            return $this->fetchApiPrice($service, $country);
        });

        if ($apiPrice === null) {
            // If API fails, return base service price
            return $service->price;
        }

        // Apply markup percentage
        $markupPercentage = $generalSettings->api_price_markup_percentage ?? 20.00;
        $finalPrice = $apiPrice * (1 + ($markupPercentage / 100));
        
        return round($finalPrice, 2);
    }

    /**
     * Fetch actual price from SMS Activate API.
     */
    private function fetchApiPrice($service, $country)
    {
        try {
            // Use the existing SMS Activate service to get prices
            $prices = $this->smsActivateService->getPrices($country->code, $service->code);
            
            if (isset($prices[$service->code])) {
                return $prices[$service->code];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to fetch API price', [
                'service_code' => $service->code,
                'country_code' => $country->code,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Update or create pivot table entry for a service-country combination.
     */
    public function updateCountryServicePrice($serviceId, $countryId, $price, $isActive = true)
    {
        $service = Service::find($serviceId);
        
        if (!$service) {
            return false;
        }

        $service->countries()->syncWithoutDetaching([
            $countryId => [
                'price' => $price,
                'is_active' => $isActive,
                'updated_at' => now()
            ]
        ]);

        return true;
    }

    /**
     * Get all available services with prices for a country.
     */
    public function getCountryServicePrices($countryId)
    {
        $services = Service::where('status', 'active')->get();
        $prices = [];

        foreach ($services as $service) {
            $price = $this->getServicePrice($service->id, $countryId);
            if ($price !== null) {
                $prices[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_code' => $service->code,
                    'price' => $price,
                    'allow_refunds' => $service->allow_refunds
                ];
            }
        }

        return $prices;
    }
}