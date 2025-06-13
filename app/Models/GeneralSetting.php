<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'contact_email',
        'contact_phone',
        'contact_address',
        'currency_name',
        'currency_icon',
        'api_price_markup_percentage',
        'enable_dynamic_pricing',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'api_price_markup_percentage' => 'decimal:2',
        'enable_dynamic_pricing' => 'boolean',
    ];
}