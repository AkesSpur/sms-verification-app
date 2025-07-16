<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SocialMediaProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_1000',
        'min_quantity',
        'max_quantity',
        'status',
        'sort_order',
        'external_service_id'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'price_per_1000' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'status' => 'boolean',
        'sort_order' => 'integer',
        'external_service_id' => 'integer'
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
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(SocialMediaCategory::class, 'category_id');
    }

    /**
     * Get all orders for this product.
     */
    public function orders()
    {
        return $this->hasMany(SocialMediaOrder::class, 'product_id');
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
     * Calculate total price based on quantity.
     */
    public function calculatePrice($quantity)
    {
        return ($quantity / 1000) * $this->price_per_1000;
    }

    /**
     * Get formatted price per 1000.
     */
    public function getFormattedPricePer1000Attribute()
    {
        return '₦' . number_format($this->price_per_1000);
    }

    /**
     * Validate quantity against min/max limits.
     */
    public function isValidQuantity($quantity)
    {
        return $quantity >= $this->min_quantity && $quantity <= $this->max_quantity;
    }

    /**
     * Get quantity validation message.
     */
    public function getQuantityValidationMessage()
    {
        return "Quantity must be between {$this->min_quantity} and {$this->max_quantity}";
    }
}