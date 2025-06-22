<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GiftOrder extends Model
{
    use HasFactory, ImageUploadTrait;

    protected $fillable = [
        'user_id',
        'gift_id',
        'quantity',
        'unit_price',
        'customization_cost',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'order_number',
        'ordered_at',
        'recipient_name',
        'recipient_phone',
        'sender_name',
        'sender_phone',
        'sender_email',
        'delivery_address',
        'delivery_apartment',
        'delivery_city',
        'delivery_state',
        'delivery_country',
        'delivery_zip',
        'is_customized',
        'custom_image',
        'custom_message',
        'gift_message',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'notes'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'gift_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'customization_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_customized' => 'boolean',
        'ordered_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'GFT-' . strtoupper(uniqid());
            }
            if (empty($model->ordered_at)) {
                $model->ordered_at = Carbon::now();
            }
        });

        static::deleting(function ($giftOrder) {
            // Delete custom image if exists
            if ($giftOrder->custom_image) {
                $giftOrder->deleteImage($giftOrder->custom_image);
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the gift that was ordered.
     */
    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include shipped orders.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include failed orders.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if order is pending.
     */
    public function isPending()
    {
        return $this->status == 'pending';
    }

    /**
     * Check if order is confirmed.
     */
    public function isConfirmed()
    {
        return $this->status == 'confirmed';
    }

    /**
     * Check if order is processing.
     */
    public function isProcessing()
    {
        return $this->status == 'processing';
    }

    /**
     * Check if order is shipped.
     */
    public function isShipped()
    {
        return $this->status == 'shipped';
    }

    /**
     * Check if order is delivered.
     */
    public function isDelivered()
    {
        return $this->status == 'delivered';
    }

    /**
     * Check if order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status == 'cancelled';
    }

    /**
     * Check if order failed.
     */
    public function isFailed()
    {
        return $this->status == 'failed';
    }

    /**
     * Mark order as confirmed.
     */
    public function markAsConfirmed()
    {
        $this->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);
    }
    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid'
        ]);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped($trackingNumber = null)
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipped_at' => Carbon::now()
        ]);
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);
    }

    /**
     * Mark order as cancelled.
     */
    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason
        ]);
    }

    /**
     * Mark order as failed.
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'payment_status' => 'failed',
            'notes' => $reason
        ]);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '₦' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted customization cost.
     */
    public function getFormattedCustomizationCostAttribute()
    {
        return '₦' . number_format($this->customization_cost, 2);
    }

    /**
     * Get the custom image URL.
     */
    public function getCustomImageUrlAttribute()
    {
        if ($this->custom_image) {
            return asset($this->custom_image);
        }
        return null;
    }

    /**
     * Get full delivery address.
     */
    public function getFullDeliveryAddressAttribute()
    {
        $address = $this->delivery_address;
        $address .= ', ' . $this->delivery_city;
        $address .= ', ' . $this->delivery_state;
        if ($this->delivery_zip) {
            $address .= ' ' . $this->delivery_zip;
        }
        $address .= ', ' . $this->delivery_country;
        
        return $address;
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}