<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewQueue extends Model
{
    protected $fillable = [
        'order_id', 'reason'
    ];

    /**
     * Get the order that belongs to the review queue.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}