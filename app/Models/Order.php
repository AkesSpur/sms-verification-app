<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderExpiredNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
protected $fillable = [
    'user_id', 'service_id', 'country_id', 'phone_number', 'activation_id', 'price', 'api_price', 'markup_percentage', 'final_price',
    'sms_code', 'sms_received_at', 'status', 'api_response', 'api_status', 'refunded', 'needs_review', 
    'retry_attempts', 'last_retry_at', 'max_retries', 'expires_at', 'sms_window_expires_at', 'is_number_used',
    'can_cancel', 'cancelled_at', 'cancellation_reason', 'order_source', 'api_provider'
];

protected $casts = [
    'expires_at' => 'datetime',
    'sms_window_expires_at' => 'datetime',
    'sms_received_at' => 'datetime',
    'last_retry_at' => 'datetime',
    'cancelled_at' => 'datetime',
    'price' => 'decimal:2',
    'api_price' => 'decimal:2',
    'markup_percentage' => 'decimal:2',
    'final_price' => 'decimal:2',
    'refunded' => 'boolean',
    'needs_review' => 'boolean',
    'is_number_used' => 'boolean',
    'can_cancel' => 'boolean',
];

// Order status constants
const STATUS_PENDING = 'pending';
const STATUS_ACTIVE = 'active';
const STATUS_COMPLETED = 'completed';
const STATUS_EXPIRED = 'expired';
const STATUS_CANCELLED = 'cancelled';
const STATUS_FAILED = 'failed';
const STATUS_REFUNDED = 'refunded';

// SMS window duration in minutes (SMSActivate gives 20 minutes)
const SMS_WINDOW_MINUTES = 20;

/**
 * Get the user that owns the order.
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Get the service for this order.
 */
public function service()
{
    return $this->belongsTo(Service::class);
}

/**
 * Get the country for this order.
 */
public function country()
{
    return $this->belongsTo(Country::class, 'country_id');
}

/**
 * Get the review queue entry for this order.
 */
public function reviewQueue()
{
    return $this->hasOne(ReviewQueue::class);
}

/**
 * Get the status history for this order.
 */
public function statusHistory()
{
    return $this->hasMany(OrderStatus::class)->orderBy('changed_at', 'desc');
}

/**
 * Get the latest status change.
 */
public function latestStatusChange()
{
    return $this->hasOne(OrderStatus::class)->latest('changed_at');
}

public function isExpired()
{
    return $this->expires_at && $this->expires_at->isPast();
}

/**
 * Check if the SMS window has expired (20 minutes from order creation)
 */
public function isSmsWindowExpired()
{
    return $this->sms_window_expires_at && $this->sms_window_expires_at->isPast();
}

/**
 * Check if the order can be cancelled
 */
public function canBeCancelled()
{
    return $this->can_cancel && 
           in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACTIVE]) &&
           !$this->isSmsWindowExpired() &&
           !$this->sms_received_at;
}

/**
 * Check if the order should be auto-cancelled due to SMS window expiry
 */
public function shouldBeAutoCancelled()
{
    return $this->isSmsWindowExpired() && 
           !$this->sms_received_at && 
           in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACTIVE]);
}

/**
 * Set the SMS window expiration time
 */
public function setSmsWindowExpiration()
{
    $this->update([
        'sms_window_expires_at' => Carbon::now()->addMinutes(self::SMS_WINDOW_MINUTES),
        'can_cancel' => true
    ]);
}

/**
 * Mark SMS as received
 */
public function markSmsReceived($smsCode)
{
    $this->update([
        'sms_code' => $smsCode,
        'sms_received_at' => Carbon::now(),
        'status' => self::STATUS_COMPLETED,
        'can_cancel' => false
    ]);
    
    $this->logStatusChange(self::STATUS_COMPLETED, 'SMS received');
}

/**
 * Cancel the order
 */
public function cancel($reason = null, $changedBy = 'system', $changedById = null)
{
    if (!$this->canBeCancelled() && !$this->shouldBeAutoCancelled()) {
        throw new \Exception('Order cannot be cancelled');
    }

    DB::transaction(function () use ($reason, $changedBy, $changedById) {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => $reason,
            'can_cancel' => false
        ]);
        
        // Process refund for the cancelled order
        $this->processRefund($reason, $changedBy);
        
        $this->logStatusChange(self::STATUS_CANCELLED, $reason, $changedBy, $changedById);
    });
}

/**
 * Mark as refunded
 */
public function markAsRefunded($reason = null)
{
    $this->update([
        'refunded' => true,
        'status' => self::STATUS_REFUNDED,
        'can_cancel' => false
    ]);
    
    $this->logStatusChange(self::STATUS_REFUNDED, $reason);
}

/**
 * Mark as failed
 */
public function markAsFailed($reason = null)
{
    $this->update([
        'status' => self::STATUS_FAILED,
        'can_cancel' => false
    ]);
    
    $this->logStatusChange(self::STATUS_FAILED, $reason);
}

/**
 * Log status change
 */
public function logStatusChange($newStatus, $reason = null, $changedBy = 'system', $changedById = null)
{
    $this->statusHistory()->create([
        'status' => $newStatus,
        'previous_status' => $this->getOriginal('status'),
        'reason' => $reason,
        'changed_by_type' => $changedBy,
        'changed_by_id' => $changedById,
        'changed_at' => Carbon::now()
    ]);
}

public function handleExpiration()
{
    if ($this->status !== 'pending' || !$this->isExpired()) {
        return;
    }

    DB::transaction(function () {
        $this->update(['status' => 'expired']);

        if ($this->shouldRefund()) {
            $this->processRefund('auto_expired', 'system');
        }

        if ($this->shouldBlacklist()) {
            BlacklistedNumber::create([
                'number' => $this->phone_number,
                'service_id' => $this->service_id,
                'reason' => 'auto_expired'
            ]);
        }

        $this->user->notify(new OrderExpiredNotification($this));
    });
}

public function shouldRefund()
{
    return is_null($this->sms_code) &&
           !$this->is_number_used &&
           $this->service->allow_refunds;
}

public function shouldBlacklist()
{
    return Order::where('phone_number', $this->phone_number)
        ->where('status', 'expired')
        ->count() >= config('sms.blacklist_threshold');
}

public function isSuspicious()
{
    return Order::where('user_id', $this->user_id)
        ->where('status', 'expired')
        ->where('created_at', '>', now()->subDay())
        ->count() > config('sms.fraud_threshold');
}

public function flagForReview()
{
    if ($this->isSuspicious()) {
        ReviewQueue::create([
            'order_id' => $this->id,
            'reason' => 'multiple_failures'
        ]);
        $this->update(['needs_review' => true]);
    }
}

public function markAsBlacklisted()
{
    $this->update([
        'needs_review' => true,
        'status' => 'blacklisted'
    ]);
}

public function markAsSuspicious()
{
    $this->update([
        'needs_review' => true,
        'status' => 'suspicious'
    ]);
}

public function incrementRetryAttempts()
{
    $this->update([
        'retry_attempts' => $this->retry_attempts + 1,
        'last_retry_at' => Carbon::now()
    ]);
}

public function canRetry()
{
    $maxRetries = $this->max_retries ?? 3;
    return $this->retry_attempts < $maxRetries;
}

public function shouldBeReviewed()
{
    return $this->needs_review || $this->retry_attempts >= ($this->max_retries ?? 3);
}

/**
 * Scope for orders that need auto-cancellation
 */
public function scopeNeedsAutoCancellation($query)
{
    return $query->where('sms_window_expires_at', '<', Carbon::now())
                ->whereNull('sms_received_at')
                ->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACTIVE]);
}

/**
 * Scope for active orders
 */
public function scopeActive($query)
{
    return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACTIVE]);
}

public function getStatusColorAttribute()
{
    return match($this->status) {
        self::STATUS_PENDING => 'warning',
        self::STATUS_ACTIVE => 'info',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_CANCELLED => 'secondary',
        self::STATUS_REFUNDED => 'danger',
        self::STATUS_EXPIRED => 'dark',
        self::STATUS_FAILED => 'danger',
        'blacklisted' => 'dark',
        'suspicious' => 'warning',
        default => 'secondary'
    };
}

public function getStatusTextAttribute()
{
    return match($this->status) {
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_REFUNDED => 'Refunded',
        self::STATUS_EXPIRED => 'Expired',
        self::STATUS_FAILED => 'Failed',
        'blacklisted' => 'Blacklisted',
        'suspicious' => 'Suspicious',
        default => ucfirst($this->status)
    };
}

/**
 * Process refund for a cancelled order
 *
 * @param string|null $reason
 * @param string $changedBy
 * @return void
 */
public function processRefund($reason = null, $changedBy = 'system')
{
    try {
        // Check if order has already been refunded to prevent multiple refunds
        if ($this->refunded) {
            Log::warning("Attempted to refund order #{$this->id} that has already been refunded", [
                'order_id' => $this->id,
                'user_id' => $this->user_id,
                'reason' => $reason,
                'changed_by' => $changedBy
            ]);
            return;
        }
        
        // Get the user
        $user = User::findorFail($this->user_id);
        
        if (!$user) {
            Log::error("Cannot process refund for order #{$this->id}: User not found", [
                'order_id' => $this->id,
                'user_id' => $this->user_id
            ]);
            return;
        }

        // Add the order price back to user's balance
        $user->increment('balance', $this->final_price);

        // Log the refund transaction
        Transaction::createTransaction(
            $user,
            'credit',
            'sms_refund',
            $this->final_price,
            "Refund for cancelled order #{$this->id} - {$this->service->name}" . ($reason ? " ({$reason})" : ''),
            ['service_code' => $this->service->code, 'country_code' => $this->country->code],
            $this
        );

        // Mark order as refunded
        $this->update(['refunded' => true]);

        Log::info("Processed refund for order #{$this->id}", [
            'order_id' => $this->id,
            'user_id' => $user->id,
            'refund_amount' => $this->final_price,
            'changed_by' => $changedBy,
            'reason' => $reason,
            'new_balance' => $user->fresh()->balance
        ]);

    } catch (\Exception $e) {
        Log::error("Failed to process refund for order #{$this->id}", [
            'order_id' => $this->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
}
