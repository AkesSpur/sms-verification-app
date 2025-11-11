<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'bank_code',
        'account_number',
        'account_name',
        'bank_name',
        'reserved_account_id',
        'provider',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}