<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ResellerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        // 'log_id', // deprecated; logs now reference order via logs.order_id
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
        // 'log_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'purchased_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'RPO-' . strtoupper(uniqid());
            }
            if (empty($model->purchased_at)) {
                $model->purchased_at = Carbon::now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(ResellerProduct::class, 'product_id');
    }

    public function logs()
    {
        return $this->hasMany(ResellerProductLog::class, 'order_id');
    }

    // Backward-compat: single log relation if present (legacy orders)
    public function log()
    {
        return $this->belongsTo(ResellerProductLog::class, 'log_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isCompleted()
    {
        return $this->status == 'completed';
    }

    public function isPending()
    {
        return $this->status == 'pending';
    }

    public function isFailed()
    {
        return $this->status == 'failed';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'payment_status' => 'failed',
            'notes' => $reason
        ]);
    }

    public function getFormattedTotalAttribute()
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return '₦' . number_format($this->unit_price, 2);
    }
}