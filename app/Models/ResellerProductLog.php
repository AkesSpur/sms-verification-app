<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ResellerProductLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'log_item',
        'details',
        'status',
        'sold_at',
        'sold_to_user_id',
        'order_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sold_at' => 'datetime',
        'sold_to_user_id' => 'integer',
        'order_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(ResellerProduct::class, 'product_id');
    }

    public function soldToUser()
    {
        return $this->belongsTo(User::class, 'sold_to_user_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function markAsSold($userId = null, $orderId = null)
    {
        $this->update([
            'status' => 'sold',
            'sold_at' => Carbon::now(),
            'sold_to_user_id' => $userId,
            'order_id' => $orderId,
        ]);

        $this->product->updateStock();
    }

    public function markAsAvailable()
    {
        $this->update([
            'status' => 'available',
            'sold_at' => null,
            'sold_to_user_id' => null,
            'order_id' => null,
        ]);

        $this->product->updateStock();
    }

    public function order()
    {
        return $this->belongsTo(ResellerOrder::class, 'order_id');
    }
}