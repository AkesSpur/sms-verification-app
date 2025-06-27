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
     * Compares API price with database price and uses higher value.
     * Returns price in Naira.
     */
    public function getServicePrice($serviceId, $countryId)
    {
        Log::info('🔍 Starting price calculation', [
            'service_id' => $serviceId,
            'country_id' => $countryId
        ]);
        
        $service = Service::find($serviceId);
        $country = Country::find($countryId);
        
        if (!$service || !$country) {
            Log::warning('❌ Service or country not found', [
                'service_found' => $service ? true : false,
                'country_found' => $country ? true : false,
                'service_id' => $serviceId,
                'country_id' => $countryId
            ]);
            return null;
        }

        Log::info('✅ Service and country found', [
            'service_code' => $service->code,
            'service_name' => $service->name,
            'country_code' => $country->code,
            'country_name' => $country->name
        ]);

        $generalSettings = GeneralSetting::first();
        $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
        
        Log::info('⚙️ General settings loaded', [
            'exchange_rate' => $exchangeRate,
            'dynamic_pricing_enabled' => $generalSettings->enable_dynamic_pricing ?? false,
            'markup_percentage' => $generalSettings->api_price_markup_percentage ?? 20.00
        ]);

        // Check if price exists in pivot table
        $pivotPrice = $this->getPivotTablePrice($service, $countryId);
        Log::info('💾 Pivot table price check', [
            'pivot_price' => $pivotPrice,
            'has_pivot_price' => $pivotPrice !== null
        ]);
        
        // Get API price in USD and convert to Naira
        $apiPriceInNaira = $this->getApiPriceInNaira($service, $country, $exchangeRate);
        Log::info('🌐 API price check', [
            'api_price_naira' => $apiPriceInNaira,
            'has_api_price' => $apiPriceInNaira !== null
        ]);
        
        // If pivot price is set, use it (priority over API price)
        if ($pivotPrice !== null) {
            $roundedPrice = $this->roundToNextTenth($pivotPrice);
            Log::info('📊 Using pivot price (priority)', [
                'pivot_price' => $pivotPrice,
                'final_price' => $roundedPrice
            ]);
            return $roundedPrice;
        }
        
        // If no pivot price, use API price
        if ($apiPriceInNaira !== null) {
            $roundedPrice = $this->roundToNextTenth($apiPriceInNaira);
            Log::info('📊 Using API price (no pivot price)', [
                'api_price' => $apiPriceInNaira,
                'final_price' => $roundedPrice
            ]);
            return $roundedPrice;
        }
        
        // No price available
        Log::warning('❌ No price available (neither pivot nor API)');
        return null;
    }

    /**
     * Get price from pivot table if exists and is active.
     */
    private function getPivotTablePrice($service, $countryId)
    {
        Log::info('🔍 Checking pivot table for price', [
            'service_code' => $service->code,
            'country_id' => $countryId
        ]);
        
        $countryService = $service->countries()->where('country_id', $countryId)->first();
        
        if (!$countryService) {
            Log::info('❌ No pivot table entry found', [
                'service_code' => $service->code,
                'country_id' => $countryId
            ]);
            return null;
        }
        
        Log::info('✅ Pivot table entry found', [
            'service_code' => $service->code,
            'country_id' => $countryId,
            'is_active' => $countryService->pivot->is_active,
            'price' => $countryService->pivot->price ?? 'null',
            'final_price' => $countryService->pivot->final_price ?? 'null',
            'pivot_data' => $countryService->pivot->toArray()
        ]);
        
        if ($countryService && $countryService->pivot->is_active) {
            $finalPrice = $countryService->pivot->final_price ?? $countryService->pivot->price;
            Log::info('💰 Returning pivot price', [
                'final_price' => $finalPrice,
                'source' => $countryService->pivot->final_price ? 'final_price' : 'price'
            ]);
            return $finalPrice;
        }
        
        Log::info('❌ Pivot entry exists but not active', [
            'is_active' => $countryService->pivot->is_active
        ]);
        return null;
    }

    /**
     * Get API price in Naira (converted from USD).
     */
    private function getApiPriceInNaira($service, $country, $exchangeRate)
    {
        Log::info('🌐 Starting API price calculation', [
            'service_code' => $service->code,
            'country_code' => $country->code,
            'exchange_rate' => $exchangeRate
        ]);
        
        $generalSettings = GeneralSetting::first();
        
        if (!$generalSettings || !$generalSettings->enable_dynamic_pricing) {
            Log::info('❌ Dynamic pricing disabled or no settings', [
                'has_settings' => $generalSettings ? true : false,
                'dynamic_pricing_enabled' => $generalSettings->enable_dynamic_pricing ?? false
            ]);
            return null;
        }

        Log::info('✅ Dynamic pricing enabled', [
            'markup_percentage' => $generalSettings->api_price_markup_percentage ?? 20.00
        ]);

        // Cache key for API price
        $cacheKey = "api_price_{$service->code}_{$country->code}";
        
        Log::info('🔍 Checking cache for API price', [
            'cache_key' => $cacheKey
        ]);
        
        // Try to get cached price (cache for 30 minutes)
        $apiPriceUsd = Cache::remember($cacheKey, 1800, function () use ($service, $country) {
            Log::info('📡 Cache miss - fetching from API', [
                'service_code' => $service->code,
                'country_code' => $country->code
            ]);
            return $this->fetchApiPrice($service, $country);
        });

        Log::info('💵 API price result', [
            'api_price_usd' => $apiPriceUsd,
            'has_price' => $apiPriceUsd !== null
        ]);

        if ($apiPriceUsd === null) {
            Log::info('❌ No API price available');
            return null;
        }

        // Apply markup percentage
        $markupPercentage = $generalSettings->api_price_markup_percentage ?? 20.00;
        $finalPriceUsd = $apiPriceUsd * (1 + ($markupPercentage / 100));
        
        // Convert to Naira
        $finalPriceNaira = $finalPriceUsd * $exchangeRate;
        $roundedPrice = round($finalPriceNaira, 2);
        
        Log::info('💰 Final API price calculation', [
            'original_usd' => $apiPriceUsd,
            'markup_percentage' => $markupPercentage,
            'marked_up_usd' => $finalPriceUsd,
            'exchange_rate' => $exchangeRate,
            'final_naira' => $finalPriceNaira,
            'rounded_naira' => $roundedPrice
        ]);
        
        return $roundedPrice;
    }

    /**
     * Fetch actual price from SMS Activate API.
     */
    private function fetchApiPrice($service, $country)
    {
        Log::info('📡 Fetching price from SMS Activate API', [
            'service_code' => $service->code,
            'country_code' => $country->code
        ]);
        
        try {
            // Use the existing SMS Activate service to get prices
            $prices = $this->smsActivateService->getPrices($country->code, $service->code);
            
            Log::info('📋 SMS Activate API response', [
                'service_code' => $service->code,
                'country_code' => $country->code,
                'raw_prices' => $prices
            ]);
            
            // Extract price from nested structure: {"country_id":{"service_code":{"cost":price}}}
            $countryCode = (string)$country->code;
            $serviceCode = $service->code;
            
            if (isset($prices[$countryCode][$serviceCode]['cost'])) {
                $cost = $prices[$countryCode][$serviceCode]['cost'];
                Log::info('✅ Price found in API response', [
                    'service_code' => $serviceCode,
                    'country_code' => $countryCode,
                    'price_usd' => $cost,
                    'count' => $prices[$countryCode][$serviceCode]['count'] ?? 'unknown'
                ]);
                return $cost;
            }
            
            Log::info('❌ Service price not found in API response', [
                'service_code' => $serviceCode,
                'country_code' => $countryCode,
                'available_countries' => array_keys($prices),
                'available_services_for_country' => isset($prices[$countryCode]) ? array_keys($prices[$countryCode]) : 'country_not_found'
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch API price', [
                'service_code' => $service->code,
                'country_code' => $country->code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Update or create pivot table entry for a service-country combination.
     * Price should be provided in Naira.
     */
    public function updateCountryServicePrice($serviceId, $countryId, $price, $isActive = true)
    {
        $service = Service::find($serviceId);
        
        if (!$service) {
            return false;
        }

        $service->countries()->syncWithoutDetaching([
            $countryId => [
                'final_price' => $price,
                'is_active' => $isActive,
                'updated_at' => now()
            ]
        ]);

        return true;
    }

    /**
     * Get all available services with prices for a country.
     * All prices returned are in Naira.
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
                    'price' => $price, // Price in Naira
                    'allow_refunds' => $service->allow_refunds
                ];
            }
        }

        return $prices;
    }

    /**
     * Convert USD price to Naira using current exchange rate.
     */
    public function convertUsdToNaira($usdAmount)
    {
        $generalSettings = GeneralSetting::first();
        $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
        
        return round($usdAmount * $exchangeRate, 2);
    }

    /**
     * Convert Naira price to USD using current exchange rate.
     */
    public function convertNairaToUsd($nairaAmount)
    {
        $generalSettings = GeneralSetting::first();
        $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
        
        return round($nairaAmount / $exchangeRate, 4);
    }



    /**
     * Calculate final API price with markup and convert to Naira.
     * Takes USD price and returns Naira price.
     */
    public function calculateApiPrice($apiPriceUsd, $markupPercentage = null)
    {
        try {
            if ($apiPriceUsd === null) {
                return null;
            }

            $generalSettings = GeneralSetting::first();
            $markup = $markupPercentage ?? $generalSettings->api_price_markup_percentage ?? 20.00;
            $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
            
            // Apply markup
            $priceWithMarkup = $apiPriceUsd * (1 + ($markup / 100));
            
            // Convert to Naira
            $finalPriceNaira = $priceWithMarkup * $exchangeRate;
            
            Log::info('💰 API price calculated', [
                'api_price_usd' => $apiPriceUsd,
                'markup_percentage' => $markup,
                'exchange_rate' => $exchangeRate,
                'final_price_naira' => $finalPriceNaira
            ]);
            
            return round($finalPriceNaira, 2);
        } catch (\Exception $e) {
            Log::error('❌ Failed to calculate API price', [
                'api_price_usd' => $apiPriceUsd,
                'markup_percentage' => $markupPercentage,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Round price to the next 10th (122 = 130, 127.1 = 130)
     */
    private function roundToNextTenth($price)
    {
        if ($price === null) {
            return null;
        }
        
        return ceil($price / 10) * 10;
    }
}