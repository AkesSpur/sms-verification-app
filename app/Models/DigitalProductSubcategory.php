<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalProductSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'category_id' => 'integer',
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
     * Get the category that owns the subcategory.
     */
    public function category()
    {
        return $this->belongsTo(DigitalProductCategory::class, 'category_id');
    }

    /**
     * Get all products for this subcategory.
     */
    public function products()
    {
        return $this->hasMany(DigitalProduct::class, 'subcategory_id');
    }

    /**
     * Get active products for this subcategory.
     */
    public function activeProducts()
    {
        return $this->hasMany(DigitalProduct::class, 'subcategory_id')
                    ->where('status', 1)
                    ->orderBy('sort_order');
    }

    /**
     * Scope a query to only include active subcategories.
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
}