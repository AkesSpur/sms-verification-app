<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
protected $fillable = [
    'name', 'code', 'price', 'allow_refunds', 'status'
];

protected $casts = [
    'price' => 'decimal:2',
    'allow_refunds' => 'boolean',
];

/**
 * Get the countries for this service with pricing.
 */
public function countries()
{
    return $this->belongsToMany(Country::class)
                ->withPivot('price', 'is_active')
                ->withTimestamps();
}

/**
 * Get active countries for this service.
 */
public function activeCountries()
{
    return $this->countries()->wherePivot('is_active', true);
}

/**
 * Get price for a specific country.
 * If not in pivot table, fetch from API and apply markup.
 */
public function getPriceForCountry($countryId)
{
    // First check if price exists in pivot table
    $countryService = $this->countries()->where('country_id', $countryId)->first();
    
    if ($countryService && $countryService->pivot->is_active) {
        return $countryService->pivot->price;
    }
    
    // If not in pivot table, fetch from API and apply markup
    return $this->fetchApiPriceWithMarkup($countryId);
}

/**
 * Fetch price from API and apply markup percentage.
 */
private function fetchApiPriceWithMarkup($countryId)
{
    $generalSettings = GeneralSetting::first();
    
    if (!$generalSettings || !$generalSettings->enable_dynamic_pricing) {
        return $this->price; // Return base price if dynamic pricing is disabled
    }
    
    // TODO: Implement actual API call to fetch price
    // For now, using base price as fallback
    $apiPrice = $this->price;
    
    // Apply markup percentage
    $markupPercentage = $generalSettings->api_price_markup_percentage ?? 20.00;
    $finalPrice = $apiPrice * (1 + ($markupPercentage / 100));
    
    return round($finalPrice, 2);
}

/**
 * Get the orders for the service.
 */
public function orders()
{
    return $this->hasMany(Order::class);
}

/**
 * Get the blacklisted numbers for the service.
 */
public function blacklistedNumbers()
{
    return $this->hasMany(BlacklistedNumber::class);
}
}
