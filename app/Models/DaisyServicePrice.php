<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DaisyServicePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'country_code',
        'country_name',
        'price_usd',
        'price_naira',
        'markup_percentage',
        'status',
        'api_price_id',
        'last_updated_from_api'
    ];

    protected $casts = [
        'price_usd' => 'decimal:4',
        'price_naira' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'status' => 'boolean',
        'last_updated_from_api' => 'datetime'
    ];

    protected $appends = [
        'final_price_naira',
        'final_price_usd',
        'formatted_price_naira',
        'formatted_price_usd'
    ];

    /**
     * Relationship with DaisyService
     */
    public function service()
    {
        return $this->belongsTo(DaisyService::class, 'service_id');
    }

    /**
     * Relationship with Daisy Orders
     */
    public function daisyOrders()
    {
        return $this->hasMany(DaisyOrder::class, 'country_code', 'country_code')
                    ->where('service_code', $this->service->code ?? null);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeCheapest($query)
    {
        return $query->orderBy('price_naira', 'asc');
    }

    public function scopeMostExpensive($query)
    {
        return $query->orderBy('price_naira', 'desc');
    }

    // Accessors
    public function getFinalPriceNairaAttribute()
    {
        $basePrice = $this->price_naira;
        $markup = $this->markup_percentage ?? 0;
        
        return round($basePrice + ($basePrice * $markup / 100), 2);
    }

    public function getFinalPriceUsdAttribute()
    {
        $basePrice = $this->price_usd;
        $markup = $this->markup_percentage ?? 0;
        
        return round($basePrice + ($basePrice * $markup / 100), 4);
    }

    public function getFormattedPriceNairaAttribute()
    {
        return '₦' . number_format($this->final_price_naira, 2);
    }

    public function getFormattedPriceUsdAttribute()
    {
        return '$' . number_format($this->final_price_usd, 4);
    }

    public function getCountryFlagAttribute()
    {
        // Return country flag emoji or image URL
        $flags = [
            'ng' => '🇳🇬',
            'us' => '🇺🇸',
            'uk' => '🇬🇧',
            'ca' => '🇨🇦',
            'au' => '🇦🇺',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'in' => '🇮🇳',
            'br' => '🇧🇷',
            'mx' => '🇲🇽'
        ];
        
        return $flags[strtolower($this->country_code)] ?? '🏳️';
    }

    // Methods
    public function updatePrice($priceUsd, $priceNaira = null, $markupPercentage = null)
    {
        $this->price_usd = $priceUsd;
        $settings = GeneralSetting::first();
        
        if ($priceNaira) {
            $this->price_naira = $priceNaira;
        } else {
            // Convert USD to Naira first, then apply markup percentage
            $usdToNairaRate = $settings->usd_to_ngn_rate ?? 1600;
            $baseNairaPrice = $priceUsd * $usdToNairaRate;
            $markup = $markupPercentage ?? $settings->api_price_markup_percentage ?? 0;
            $finalPrice = $baseNairaPrice * (1 + ($markup / 100));
            // Round to nearest tenth (134 -> 140, 1227 -> 1230)
            $this->price_naira = ceil($finalPrice / 10) * 10;
        }
        
        if ($markupPercentage !== null) {
            $this->markup_percentage = $markupPercentage;
        }
        
        $this->last_updated_from_api = now();
        
        return $this->save();
    }

    public function toggleAvailability()
    {
        $this->status = !$this->status;
        return $this->save();
    }

    public function applyMarkup($percentage)
    {
        $this->markup_percentage = $percentage;
        return $this->save();
    }

    // Static methods
    public static function getCheapestForService($serviceId)
    {
        return static::byService($serviceId)
                    ->active()
                    ->cheapest()
                    ->first();
    }

    public static function getAvailableCountriesForService($serviceId)
    {
        return static::byService($serviceId)
                    ->active()
                    ->distinct()
                    ->pluck('country_code', 'country_name');
    }

    public static function bulkUpdatePrices(array $updates)
    {
        $updatedCount = 0;
        
        foreach ($updates as $update) {
            if (!isset($update['service_id'], $update['country_code'])) {
                continue;
            }
            
            $servicePrice = static::where('service_id', $update['service_id'])
                                 ->where('country_code', $update['country_code'])
                                 ->first();
            
            if ($servicePrice) {
                $servicePrice->updatePrice(
                    $update['price_usd'] ?? $servicePrice->price_usd,
                    $update['price_naira'] ?? null,
                    $update['markup_percentage'] ?? $servicePrice->markup_percentage
                );
                $updatedCount++;
            }
        }
        
        return $updatedCount;
    }

    public static function bulkApplyMarkup($serviceId, $percentage, $countryCode = null)
    {
        $query = static::byService($serviceId);
        
        if ($countryCode) {
            $query->byCountry($countryCode);
        }
        
        return $query->update(['markup_percentage' => $percentage]);
    }

    public static function getStatistics($serviceId = null)
    {
        $query = static::query();
        
        if ($serviceId) {
            $query->byService($serviceId);
        }
        
        return [
            'total_prices' => $query->count(),
            'active_prices' => $query->active()->count(),
            'countries_count' => $query->distinct('country_code')->count(),
            'average_price_naira' => $query->active()->avg('price_naira'),
            'min_price_naira' => $query->active()->min('price_naira'),
            'max_price_naira' => $query->active()->max('price_naira'),
            'average_markup' => $query->active()->avg('markup_percentage')
        ];
    }

    public static function syncFromApiData($serviceId, $apiData, $usdToNairaRate = 1600)
    {
        $syncedCount = 0;
        
        // Get markup percentage from general settings
        $generalSettings = GeneralSetting::first();

        $markup = $generalSettings->api_price_markup_percentage ?? 0;
        $dbRate = $generalSettings->usd_to_ngn_rate;
        
            Log::info('db Rate: ' . $dbRate);
            Log::info('mark up: ' . $markup. ':' . 'Rate: ' . $usdToNairaRate);

        foreach ($apiData as $countryCode => $priceInfo) {
            $usdPrice = is_array($priceInfo) ? ($priceInfo['price_usd'] ?? 0) : $priceInfo;
            Log::info('USD Price: ' . $usdPrice);
            
            // Convert USD to Naira first, then apply markup percentage
            $baseNairaPrice = $usdPrice * $usdToNairaRate;

            Log::info('Base Naira Price: ' . $baseNairaPrice);

            $finalPrice = $baseNairaPrice * (1 + ($markup / 100));

            Log::info('Final Naira Price: ' . $finalPrice);
            // Round to nearest tenth (134 -> 140, 1227 -> 1230)
            $nairaPrice = ceil($finalPrice / 10) * 10;
            
            $isActive = is_array($priceInfo) ? ($priceInfo['available'] ?? true) : true;
            
            static::updateOrCreate(
                [
                    'service_id' => $serviceId,
                    'country_code' =>$countryCode
                ],
                [
                    'country_name' => static::getCountryName($countryCode),
                    'price_usd' => $usdPrice,
                    'price_naira' => $nairaPrice,
                    'status' => $isActive,
                    'last_updated_from_api' => now()
                ]
            );
            
            $syncedCount++;
        }
        
        return $syncedCount;
    }

    private static function getCountryName($countryCode)
    {
        $countries = [
            'ng' => 'Nigeria',
            'us' => 'United States',
            'uk' => 'United Kingdom',
            'ca' => 'Canada',
            'au' => 'Australia',
            'de' => 'Germany',
            'fr' => 'France',
            'in' => 'India',
            'br' => 'Brazil',
            'mx' => 'Mexico',
            'ru' => 'Russia',
            'cn' => 'China',
            'jp' => 'Japan',
            'kr' => 'South Korea'
        ];
        
        return $countries[strtolower($countryCode)] ?? ucfirst($countryCode);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($servicePrice) {
            if (!$servicePrice->country_name && $servicePrice->country_code) {
                $servicePrice->country_name = static::getCountryName($servicePrice->country_code);
            }
        });
    }
}