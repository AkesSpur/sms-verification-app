<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'notes',
        'admin_id',
        'processed_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'admin_id' => 'integer',
        'processed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}