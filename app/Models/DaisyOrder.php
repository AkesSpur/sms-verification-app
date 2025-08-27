<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DaisyOrder extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'rental_id',
        'phone_number',
        'service_name',
        'service_code',
        'country_name',
        'country_code',
        'price',
        'trx',
        'area_codes',
        'carrier',
        'max_price',
        'status',
        'sms_code',
        'sms_text',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'price' => 'decimal:8',
        'max_price' => 'decimal:8'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relationship with DaisyService
     */
    public function service()
    {
        return $this->belongsTo(DaisyService::class, 'service_code', 'code');
    }

    /**
     * Relationship with DaisyServicePrice
     */
    public function servicePrice()
    {
        return $this->hasOneThrough(
            DaisyServicePrice::class,
            DaisyService::class,
            'code', // Foreign key on DaisyService table
            'service_id', // Foreign key on DaisyServicePrice table
            'service_code', // Local key on DaisyOrder table
            'id' // Local key on DaisyService table
        )->where('daisy_service_prices.country_code', $this->country_code);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByService($query, $serviceCode)
    {
        return $query->where('service_code', $serviceCode);
    }

    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Methods
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function markAsActive()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save();
    }

    public function markAsCompleted($smsCode = null, $smsText = null)
    {
        $this->status = self::STATUS_COMPLETED;
        if ($smsCode) {
            $this->sms_code = $smsCode;
        }
        if ($smsText) {
            $this->sms_text = $smsText;
        }
        return $this->save();
    }

    public function markAsCancelled()
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save();
    }

    public function markAsExpired()
    {
        $this->status = self::STATUS_EXPIRED;
        return $this->save();
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACTIVE => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_EXPIRED => 'secondary'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => '#ffc107',
            self::STATUS_ACTIVE => '#007bff',
            self::STATUS_COMPLETED => '#28a745',
            self::STATUS_CANCELLED => '#dc3545',
            self::STATUS_EXPIRED => '#6c757d'
        ];

        return $colors[$this->status] ?? '#6c757d';
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price, 2);
    }

    public function getFormattedMaxPriceAttribute()
    {
        return $this->max_price ? '₦' . number_format($this->max_price, 2) : null;
    }

    public function getTimeRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        $now = Carbon::now();
        $expiresAt = Carbon::parse($this->expires_at);

        if ($now->gt($expiresAt)) {
            return 'Expired';
        }

        return $now->diffForHumans($expiresAt, true);
    }

    public function getDurationAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        $createdAt = Carbon::parse($this->created_at);
        $expiresAt = Carbon::parse($this->expires_at);

        return $createdAt->diffForHumans($expiresAt, true);
    }

    // Static methods
    public static function getStatistics($userId = null)
    {
        $baseQuery = static::query();
        
        if ($userId) {
            $baseQuery->byUser($userId);
        }
        
        return [
            'total_orders' => (clone $baseQuery)->count(),
            'pending_orders' => (clone $baseQuery)->where('status', self::STATUS_PENDING)->count(),
            'active_orders' => (clone $baseQuery)->where('status', self::STATUS_ACTIVE)->count(),
            'completed_orders' => (clone $baseQuery)->where('status', self::STATUS_COMPLETED)->count(),
            'cancelled_orders' => (clone $baseQuery)->where('status', self::STATUS_CANCELLED)->count(),
            'expired_orders' => (clone $baseQuery)->where('status', self::STATUS_EXPIRED)->count(),
            'total_spent' => (clone $baseQuery)->where('status', self::STATUS_COMPLETED)->sum('price'),
            'average_price' => (clone $baseQuery)->avg('price')
        ];
    }

    public static function getRecentOrders($limit = 10, $userId = null)
    {
        $query = static::with(['user', 'service', 'transaction'])->recent();
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        return $query->limit($limit)->get();
    }

    public static function getOrdersByStatus($status, $userId = null)
    {
        $query = static::where('status', $status);
        
        if ($userId) {
            $query->byUser($userId);
        }
        
        return $query->with(['user', 'service', 'transaction'])->recent()->get();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        // Auto-expire orders
        static::updating(function ($order) {
            if ($order->isExpired() && $order->status === self::STATUS_ACTIVE) {
                $order->status = self::STATUS_EXPIRED;
            }
        });
    }
}