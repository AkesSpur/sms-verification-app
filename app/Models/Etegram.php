<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etegram extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'country_name',
        'currency_name',
        'public_key',
        'secret_key',
        'merchant_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Check if Etegram is enabled
     */
    public function isEnabled()
    {
        return $this->status == 1;
    }

    /**
     * Get the active Etegram configuration
     */
    public static function getActiveConfig()
    {
        return self::where('status', 1)->first();
    }
}