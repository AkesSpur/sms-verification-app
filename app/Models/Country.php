<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag'
    ];

    protected $casts = [
        // 'code' is now a string (ISO code)
    ];

    /**
     * Get the services for this country with pricing.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class)
                    ->withPivot('price', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get active services for this country.
     */
    public function activeServices()
    {
        return $this->services()->wherePivot('is_active', true);
    }
}
