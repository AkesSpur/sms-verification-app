<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paystack extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'country_name',
        'currency_name',
        'public_key',
        'secret_key',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Check if Paystack is enabled
     */
    public function isEnabled()
    {
        return $this->status == 1;
    }

    /**
     * Get the active Paystack configuration
     */
    public static function getActiveConfig()
    {
        return self::where('status', 1)->first();
    }
}