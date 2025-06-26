<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localbank extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'account_name',
        'account_number',
        'bank_name',
        'extra_info'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the active local bank setting
     */
    public static function getActive()
    {
        return self::where('status', true)->first();
    }

    /**
     * Check if local bank payment is enabled
     */
    public static function isEnabled()
    {
        return self::where('status', true)->exists();
    }
}