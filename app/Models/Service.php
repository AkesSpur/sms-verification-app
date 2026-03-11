<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\SmsPoolService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Service extends Model
{
    protected $fillable = [
        'name', 'code', 'status', 'icon', 'is_available', 'available_numbers', 'last_availability_check',
        'max_retry_attempts', 'sms_timeout_minutes', 'auto_refund_on_timeout', 'api_service_code', 'api_config',
        'base_price', 'markup_percentage', 'use_dynamic_pricing', 'total_orders', 'successful_orders', 
        'success_rate', 'description', 'sort_order'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'auto_refund_on_timeout' => 'boolean',
        'use_dynamic_pricing' => 'boolean',
        'api_config' => 'array',
        'base_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'success_rate' => 'decimal:2',
        'last_availability_check' => 'datetime',
    ];

    // Service status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DEPRECATED = 'deprecated';
    const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Get the countries that this service is available in.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_service')
                    ->withPivot([
                        'price', 'is_active', 'is_available', 'available_numbers', 'last_availability_check',
                        'api_price', 'markup_percentage', 'final_price', 'last_price_update', 'total_orders',
                        'successful_orders', 'failed_orders', 'success_rate', 'max_daily_orders', 'max_hourly_orders',
                        'min_balance_required', 'status', 'last_api_response', 'last_api_check'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the orders for this service.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get active countries for this service.
     */
    public function activeCountries()
    {
        return $this->countries()
                    ->wherePivot('is_active', true)
                    ->wherePivot('is_available', true)
                    ->wherePivot('status', 'active');
    }

    /**
     * Check if the service is available.
     */
    public function isAvailable()
    {
        return $this->is_available && 
               in_array($this->status, [self::STATUS_ACTIVE]) &&
               $this->available_numbers > 0;
    }

    /**
     * Check if the service is available in a specific country.
     */
    public function isAvailableInCountry($countryCode)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $country = $this->countries()->where('code', $countryCode)->first();
        return $country && 
               $country->pivot->is_active && 
               $country->pivot->is_available &&
               $country->pivot->status === 'active' &&
               $country->pivot->available_numbers > 0;
    }

    /**
     * Get the price for a specific country in Naira.
     */
    public function getPriceForCountry($countryCode)
    {
        $country = $this->countries()->where('code', $countryCode)->first();
        if (!$country) {
            return null;
        }
        
        // Return final_price (in Naira) if available, otherwise try to fetch from API
        if ($country->pivot->final_price) {
            return $country->pivot->final_price;
        }
        
        // Try to fetch and calculate price from API
        return $this->fetchApiPriceWithMarkup($countryCode);
    }

    /**
     * Update availability for a specific country.
     */
    public function updateAvailabilityForCountry($countryCode, $availableNumbers, $apiResponse = null)
    {
        $country = Country::where('code', $countryCode)->first();
        if ($country) {
            $this->countries()->updateExistingPivot($country->id, [
                'available_numbers' => $availableNumbers,
                'is_available' => $availableNumbers > 0,
                'last_availability_check' => Carbon::now(),
                'last_api_response' => $apiResponse,
                'last_api_check' => Carbon::now()
            ]);
        }
    }

    /**
     * Update statistics for a specific country.
     */
    public function updateStatsForCountry($countryCode, $wasSuccessful = true)
    {
        $country = Country::where('code', $countryCode)->first();
        if ($country) {
            $pivot = $country->pivot;
            $totalOrders = $pivot->total_orders + 1;
            $successfulOrders = $wasSuccessful ? $pivot->successful_orders + 1 : $pivot->successful_orders;
            $failedOrders = !$wasSuccessful ? $pivot->failed_orders + 1 : $pivot->failed_orders;
            $successRate = $totalOrders > 0 ? ($successfulOrders / $totalOrders) * 100 : 0;

            $this->countries()->updateExistingPivot($country->id, [
                'total_orders' => $totalOrders,
                'successful_orders' => $successfulOrders,
                'failed_orders' => $failedOrders,
                'success_rate' => $successRate
            ]);
        }

        // Update service-level stats
        $this->increment('total_orders');
        if ($wasSuccessful) {
            $this->increment('successful_orders');
        }
        $this->update([
            'success_rate' => $this->total_orders > 0 ? ($this->successful_orders / $this->total_orders) * 100 : 0
        ]);
    }

    /**
     * Fetch API price with markup for a specific country.
     * Returns price in Naira (converted from USD).
     */
    public function fetchApiPriceWithMarkup($countryCode)
    {
        try {
            $smsPoolService = new SmsPoolService();
            $prices = $smsPoolService->getPrices($countryCode, $this->code);
            
            if (isset($prices[$this->code])) {
                $apiPriceUsd = $prices[$this->code];
                
                // Get exchange rate from general settings
                $generalSettings = GeneralSetting::first();
                $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
                
                // Use service-specific markup or default 20%
                $markup = $this->markup_percentage ?? $generalSettings->api_price_markup_percentage ?? 20;
                $finalPriceUsd = $apiPriceUsd * (1 + ($markup / 100));
                
                // Convert to Naira
                $finalPriceNaira = $finalPriceUsd * $exchangeRate;
                
                // Update the price in the pivot table
                $country = Country::where('code', $countryCode)->first();
                if ($country) {
                    $this->countries()->updateExistingPivot($country->id, [
                        'api_price' => $apiPriceUsd,
                        'markup_percentage' => $markup,
                        'final_price' => round($finalPriceNaira, 2),
                        'last_price_update' => Carbon::now()
                    ]);
                }
                
                return round($finalPriceNaira, 2);
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch API price for service ' . $this->code . ' in country ' . $countryCode . ': ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Scope for available services.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('status', self::STATUS_ACTIVE)
                    ->where('available_numbers', '>', 0);
    }

    /**
     * Scope for active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get the timeout in minutes for SMS reception.
     */
    public function getSmsTimeoutMinutes()
    {
        return $this->sms_timeout_minutes ?? Order::SMS_WINDOW_MINUTES;
    }

    /**
     * Get the maximum retry attempts.
     */
    public function getMaxRetryAttempts()
    {
        return $this->max_retry_attempts ?? 3;
    }

    /**
     * Get the blacklisted numbers for the service.
     */
    public function blacklistedNumbers()
    {
        return $this->hasMany(BlacklistedNumber::class);
    }
}
