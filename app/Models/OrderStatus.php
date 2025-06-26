<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderStatus extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'reason',
        'metadata',
        'changed_by_type',
        'changed_by_id',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the order that this status belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who changed the status (if applicable).
     */
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_id')
                    ->where('changed_by_type', 'user');
    }

    /**
     * Get the admin who changed the status (if applicable).
     */
    public function changedByAdmin()
    {
        return $this->belongsTo(User::class, 'changed_by_id')
                    ->where('changed_by_type', 'admin');
    }

    /**
     * Scope for system changes.
     */
    public function scopeSystemChanges($query)
    {
        return $query->where('changed_by_type', 'system');
    }

    /**
     * Scope for user changes.
     */
    public function scopeUserChanges($query)
    {
        return $query->where('changed_by_type', 'user');
    }

    /**
     * Scope for admin changes.
     */
    public function scopeAdminChanges($query)
    {
        return $query->where('changed_by_type', 'admin');
    }

    /**
     * Get formatted change description.
     */
    public function getChangeDescriptionAttribute()
    {
        $description = "Status changed from '{$this->previous_status}' to '{$this->status}'";
        
        if ($this->reason) {
            $description .= " - {$this->reason}";
        }
        
        return $description;
    }

    /**
     * Get the actor who made the change.
     */
    public function getActorAttribute()
    {
        return match($this->changed_by_type) {
            'system' => 'System',
            'api' => 'API',
            'user' => $this->changedByUser?->name ?? 'User',
            'admin' => $this->changedByAdmin?->name ?? 'Admin',
            default => 'Unknown'
        };
    }
}