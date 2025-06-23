<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderExpiredNotification;

class Order extends Model
{
protected $fillable = [
    'user_id', 'service_id', 'country', 'phone_number', 'activation_id', 'price', 'sms_code', 'status', 'refunded', 'needs_review', 'retry_attempts', 'expires_at', 'is_number_used'
];

protected $casts = [
    'expires_at' => 'datetime',
    'price' => 'decimal:2',
    'refunded' => 'boolean',
    'needs_review' => 'boolean',
    'is_number_used' => 'boolean',
];

/**
 * Get the user that owns the order.
 */
public function user()
{
    return $this->belongsTo(User::class);
}

/**
 * Get the service that the order belongs to.
 */
public function service()
{
    return $this->belongsTo(Service::class);
}

/**
 * Get the country that the order belongs to.
 */
public function country()
{
    return $this->belongsTo(Country::class, 'country', 'code');
}

/**
 * Get the review queue entry for the order.
 */
public function reviewQueue()
{
    return $this->hasOne(ReviewQueue::class);
}

public function isExpired()
{
    return now()->gt($this->expires_at);
}

public function handleExpiration()
{
    if ($this->status !== 'pending' || !$this->isExpired()) {
        return;
    }

    DB::transaction(function () {
        $this->update(['status' => 'expired']);

        if ($this->shouldRefund()) {
            $this->user->increment('balance', $this->service->price);
            $this->update(['refunded' => true]);
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
}
