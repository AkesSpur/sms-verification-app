<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
protected $fillable = [
    'user_id', 'service_id', 'phone_number', 'activation_id', 'sms_code', 'status', 'refunded', 'needs_review', 'retry_attempts', 'expires_at', 'is_number_used'
];

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
