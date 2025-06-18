<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gift extends Model
{
    use HasFactory, ImageUploadTrait;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'customizable',
        'customization_cost',
        'description',
        'featured_image',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'customizable' => 'boolean',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'customization_cost' => 'decimal:2'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gift) {
            if (empty($gift->slug)) {
                $gift->slug = Str::slug($gift->name);
            }
        });

        static::updating(function ($gift) {
            if ($gift->isDirty('name') && empty($gift->slug)) {
                $gift->slug = Str::slug($gift->name);
            }
        });

        static::deleting(function ($gift) {
            // Delete featured image
            if ($gift->featured_image) {
                $gift->deleteImage($gift->featured_image);
            }
        });
    }

    /**
     * Get the images for the gift.
     */
    public function images()
    {
        return $this->hasMany(GiftImage::class)->orderBy('sort_order');
    }

    /**
     * Get the featured image for the gift.
     */
    public function featuredImage()
    {
        return $this->hasOne(GiftImage::class)->where('is_featured', true);
    }

    /**
     * Get the main image for display (prioritizes featured gallery image, then featured_image field, then first gallery image).
     */
    public function getMainImageAttribute()
    {
        // First priority: Featured gallery image (is_featured = true)
        $featuredImage = $this->featuredImage;
        if ($featuredImage) {
            return $featuredImage->image_path;
        }
        
        // Second priority: Featured image from gifts table
        if ($this->featured_image) {
            return $this->featured_image;
        }
        
        // Last priority: First gallery image (only if no featured_image exists)
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->image_path;
        }
        
        return null;
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset($this->featured_image);
        }
        return null;
    }

    /**
     * Get the total price including customization if applicable.
     */
    public function getTotalPriceAttribute()
    {
        $total = $this->price;
        if ($this->customizable && $this->customization_cost) {
            $total += $this->customization_cost;
        }
        return $total;
    }

    /**
     * Scope for active gifts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for customizable gifts.
     */
    public function scopeCustomizable($query)
    {
        return $query->where('customizable', true);
    }
}