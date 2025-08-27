<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\GeneralSetting;

class DaisyService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'status',
        'sort_order',
        'is_popular',
        'meta_data'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_popular' => 'boolean',
        'meta_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the service prices for different countries
     */
    public function servicePrices()
    {
        return $this->hasMany(DaisyServicePrice::class, 'service_id');
    }

    /**
     * Get active service prices
     */
    public function activeServicePrices()
    {
        return $this->hasMany(DaisyServicePrice::class, 'service_id')->where('status', true);
    }

    /**
     * Get Daisy orders for this service
     */
    public function daisyOrders()
    {
        return $this->hasMany(DaisyOrder::class, 'service_code', 'code');
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for popular services
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for services ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get service price for a specific country
     */
    public function getPriceForCountry($countryCode)
    {
        return $this->servicePrices()
            ->where('country_code', $countryCode)
            ->where('status', true)
            ->first();
    }

    /**
     * Get the cheapest price for this service
     */
    public function getCheapestPrice()
    {
        return $this->activeServicePrices()
            ->orderBy('price_naira', 'asc')
            ->first();
    }

    /**
     * Get available countries for this service
     */
    public function getAvailableCountries()
    {
        return $this->activeServicePrices()
            ->get()
            ->pluck('country_name', 'country_code')
            ->filter();
    }

    /**
     * Check if service is available for a country
     */
    public function isAvailableForCountry($countryCode)
    {
        return $this->servicePrices()
            ->where('country_code', $countryCode)
            ->where('status', true)
            ->exists();
    }

    /**
     * Get service statistics
     */
    public function getStatistics()
    {
        return [
            'total_orders' => $this->daisyOrders()->count(),
            'active_orders' => $this->daisyOrders()->where('status', 'active')->count(),
            'completed_orders' => $this->daisyOrders()->where('status', 'completed')->count(),
            'total_revenue' => $this->daisyOrders()->where('status', 'completed')->sum('price'),
            'average_price' => $this->activeServicePrices()->avg('price_naira'),
            'min_price' => $this->activeServicePrices()->min('price_naira'),
            'max_price' => $this->activeServicePrices()->max('price_naira'),
            'countries_count' => $this->activeServicePrices()->distinct('country_code')->count()
        ];
    }

    /**
     * Update service prices in bulk
     */
    public function updatePrices($pricesData, $usdToNairaRate = 1600)
    {
        $generalSettings = GeneralSetting::first();
        $markup = $generalSettings->api_price_markup_percentage ?? 0;
        
        foreach ($pricesData as $countryCode => $priceInfo) {
            $usdPrice = is_array($priceInfo) ? ($priceInfo['price_usd'] ?? 0) : $priceInfo;
            
            // Convert USD to Naira first, then apply markup percentage
            $baseNairaPrice = $usdPrice * $usdToNairaRate;
            $finalPrice = $baseNairaPrice * (1 + ($markup / 100));
            // Round to nearest tenth (134 -> 140, 1227 -> 1230)
            $nairaPrice = ceil($finalPrice / 10) * 10;
            
            $this->servicePrices()->updateOrCreate(
                ['country_code' => $countryCode],
                [
                    'price_usd' => $usdPrice,
                    'price_naira' => $nairaPrice,
                    'status' => $priceInfo['available'] ?? true,
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: ucfirst(str_replace('_', ' ', $this->code));
    }

    /**
     * Get service icon URL
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('assets/images/services/' . $this->icon);
        }
        
        // Return default icon based on service code
        $defaultIcons = [
            'tg' => 'telegram.svg',
            'wa' => 'whatsapp.svg',
            'ig' => 'instagram.svg',
            'fb' => 'facebook.svg',
            'tw' => 'twitter.svg',
            'go' => 'google.svg',
            'ds' => 'discord.svg'
        ];
        
        $iconFile = $defaultIcons[$this->code] ?? 'default.svg';
        return asset('assets/images/services/' . $iconFile);
    }

    /**
     * Search services by name or code
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('code', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get services with their cheapest prices
     */
    public function scopeWithCheapestPrices($query)
    {
        return $query->with(['servicePrices' => function ($q) {
            $q->where('status', true)
              ->orderBy('price_naira', 'asc')
              ->limit(1);
        }]);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate sort order
        static::creating(function ($service) {
            if (is_null($service->sort_order)) {
                $service->sort_order = static::max('sort_order') + 1;
            }
        });
    }
}