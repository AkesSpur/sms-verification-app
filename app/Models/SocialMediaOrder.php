<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SocialMediaOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'social_media_link',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'order_number',
        'admin_notes',
        'purchased_at',
        'external_order_id',
        'external_status',
        'external_start_count',
        'external_remains',
        'external_charge'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'purchased_at' => 'datetime',
        'external_start_count' => 'integer',
        'external_remains' => 'integer',
        'external_charge' => 'decimal:2'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'SMO-' . strtoupper(uniqid());
            }
            if (empty($model->purchased_at)) {
                $model->purchased_at = Carbon::now();
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
     * Get the product that was ordered.
     */
    public function product()
    {
        return $this->belongsTo(SocialMediaProduct::class, 'product_id');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted()
    {
        return $this->status == 'completed';
    }

    /**
     * Check if order is pending.
     */
    public function isPending()
    {
        return $this->status == 'pending';
    }

    /**
     * Check if order is processing.
     */
    public function isProcessing()
    {
        return $this->status == 'processing';
    }

    /**
     * Check if order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status == 'cancelled';
    }

    /**
     * Mark order as completed.
     */
    public function markAsCompleted($adminNotes = null)
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'admin_notes' => $adminNotes
        ]);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing($adminNotes = null)
    {
        $this->update([
            'status' => 'processing',
            'admin_notes' => $adminNotes
        ]);
    }

    /**
     * Mark order as cancelled.
     */
    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'admin_notes' => $reason
        ]);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return '₦' . number_format($this->total_amount);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '₦' . number_format($this->unit_price);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status badge color for display.
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Update order with external API response
     */
    public function updateFromExternalApi($apiResponse)
    {
        $updateData = [];
        
        if (isset($apiResponse['order'])) {
            $updateData['external_order_id'] = $apiResponse['order'];
        }
        
        if (isset($apiResponse['status'])) {
            $updateData['external_status'] = $apiResponse['status'];
            
            // Map external status to internal status
            $updateData['status'] = $this->mapExternalStatus($apiResponse['status']);
        }
        
        if (isset($apiResponse['start_count'])) {
            $updateData['external_start_count'] = $apiResponse['start_count'];
        }
        
        if (isset($apiResponse['remains'])) {
            $updateData['external_remains'] = $apiResponse['remains'];
        }
        
        if (isset($apiResponse['charge'])) {
            $updateData['external_charge'] = $apiResponse['charge'];
        }
        
        $this->update($updateData);
    }

    /**
     * Map external API status to internal status
     */
    private function mapExternalStatus($externalStatus)
    {
        return match(strtolower($externalStatus)) {
            'pending' => 'pending',
            'in progress', 'processing' => 'processing',
            'completed', 'complete' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            'partial' => 'processing',
            default => 'pending'
        };
    }

    /**
     * Check if order has external order ID
     */
    public function hasExternalOrder()
    {
        return !empty($this->external_order_id);
    }

    /**
     * Get progress percentage based on external data
     */
    public function getProgressPercentage()
    {
        if (!$this->external_start_count || !$this->external_remains) {
            return 0;
        }
        
        $delivered = $this->external_start_count - $this->external_remains;
        return min(100, max(0, ($delivered / $this->quantity) * 100));
    }
}