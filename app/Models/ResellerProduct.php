<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResellerProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'stock',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function logs()
    {
        return $this->hasMany(ResellerProductLog::class, 'product_id');
    }

    public function availableLogs()
    {
        return $this->hasMany(ResellerProductLog::class, 'product_id')
                    ->where('status', 'available');
    }

    public function soldLogs()
    {
        return $this->hasMany(ResellerProductLog::class, 'product_id')
                    ->where('status', 'sold');
    }

    public function orders()
    {
        return $this->hasMany(ResellerOrder::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getAvailableStockAttribute()
    {
        return $this->availableLogs()->count();
    }

    public function isInStock()
    {
        return $this->available_stock > 0;
    }

    public function updateStock()
    {
        $this->update([
            'stock' => $this->available_stock
        ]);
    }
}