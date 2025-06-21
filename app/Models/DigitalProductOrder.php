<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DigitalProductOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'log_id',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'order_number',
        'purchased_at',
        'notes'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'log_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'purchased_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'DPO-' . strtoupper(uniqid());
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
        return $this->belongsTo(DigitalProduct::class, 'product_id');
    }

    /**
     * Get the specific log item that was purchased.
     */
    public function log()
    {
        return $this->belongsTo(DigitalProductLog::class, 'log_id');
    }

    /**
     * Get the log data for delivery, handling recalled logs.
     */
    public function getDeliverableLogAttribute()
    {
        $log = $this->log;
        if ($log && $log->status === 'sold') {
            return $log;
        }
        if ($log) {
            $message = "The {$log->log_item} has been recalled, if you feel this decision is wrong contact support.";
            // Return a custom object mimicking the log structure
            return (object) [
                'log_item' => $message,
                'details' => $message,
                'status' => $log->status,
                'id' => $log->id,
                'product_id' => $log->product_id
            ];
        }
        return null;
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
     * Scope a query to only include failed orders.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
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
     * Check if order failed.
     */
    public function isFailed()
    {
        return $this->status == 'failed';
    }

    /**
     * Mark order as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'paid'
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
        return '₦' . number_format($this->total_amount);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '₦' . number_format($this->unit_price);
    }
}