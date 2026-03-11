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
        'order_id', // Added order_id
        'log_item',
        'details',
        'status',
        'sold_at',
        'sold_to_user_id'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'order_id' => 'integer',
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
     * Get the order that purchased this log.
     * This is the NEW relationship using order_id column on this table.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(DigitalProductOrder::class, 'order_id');
    }

    /**
     * Legacy relationship: Get the order that purchased this log.
     * This relies on the old structure where DigitalProductOrder has log_id.
     * Kept for backward compatibility if needed, but 'purchaseOrder' is preferred for new logic.
     */
    public function legacyOrder()
    {
        return $this->hasOne(DigitalProductOrder::class, 'log_id');
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
    public function markAsSold($userId = null, $orderId = null)
    {
        $this->update([
            'status' => 'sold',
            'sold_at' => Carbon::now(),
            'sold_to_user_id' => $userId,
            'order_id' => $orderId
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
            'sold_to_user_id' => null,
            'order_id' => null
        ]);

        // Update product stock
        $this->product->updateStock();
    }
}
