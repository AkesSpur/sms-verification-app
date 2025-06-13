<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistedNumber extends Model
{
    protected $fillable = [
        'number', 'service_id', 'reason'
    ];

    /**
     * Get the service that the blacklisted number belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
