<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DigitalProductLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'log_item',
        'details',
        'status',
        'sold_at',
        'sold_to_user_id'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sold_at' => 'datetime',
        'sold_to_user_id' => 'integer'
    ];

    /**
     * Get the product that owns the log.
     */
    public function product()
    {
        return $this->belongsTo(DigitalProduct::class, 'product_id');
    }

    /**
     * Get the user who purchased this log item.
     */
    public function soldToUser()
    {
        return $this->belongsTo(User::class, 'sold_to_user_id');
    }

    /**
     * Scope a query to only include available logs.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include sold logs.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Mark this log as sold to a user.
     */
    public function markAsSold($userId = null)
    {
        $this->update([
            'status' => 'sold',
            'sold_at' => Carbon::now(),
            'sold_to_user_id' => $userId
        ]);

        // Update product stock
        $this->product->updateStock();
    }

    /**
     * Mark this log as available.
     */
    public function markAsAvailable()
    {
        $this->update([
            'status' => 'available',
            'sold_at' => null,
            'sold_to_user_id' => null
        ]);

        // Update product stock
        $this->product->updateStock();
    }

    /**
     * Get the order that purchased this log.
     */
    public function order()
    {
        return $this->hasOne(DigitalProductOrder::class, 'log_id');
    }
}