<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
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
        'subcategory_id' => 'integer',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Boot the model.
     */
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

    /**
     * Get the subcategory that owns the product.
     */
    public function subcategory()
    {
        return $this->belongsTo(DigitalProductSubcategory::class, 'subcategory_id');
    }

    /**
     * Get all logs for this product.
     */
    public function logs()
    {
        return $this->hasMany(DigitalProductLog::class, 'product_id');
    }

    /**
     * Get available logs for this product.
     */
    public function availableLogs()
    {
        return $this->hasMany(DigitalProductLog::class, 'product_id')
                    ->where('status', 'available');
    }

    /**
     * Get sold logs for this product.
     */
    public function soldLogs()
    {
        return $this->hasMany(DigitalProductLog::class, 'product_id')
                    ->where('status', 'sold');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get available stock count.
     */
    public function getAvailableStockAttribute()
    {
        return $this->availableLogs()->count();
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        return $this->available_stock > 0;
    }

    /**
     * Update stock based on available logs.
     */
    public function updateStock()
    {
        $this->update([
            'stock' => $this->available_stock
        ]);
    }

    /**
     * Get all orders for this product.
     */
    public function orders()
    {
        return $this->hasMany(DigitalProductOrder::class, 'product_id');
    }
}