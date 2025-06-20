<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GiftImage extends Model
{
    use HasFactory, ImageUploadTrait;

    protected $fillable = [
        'gift_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_featured'
    ];

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    /**
     * Get the gift that owns the image.
     */
    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }
        return null;
    }

    /**
     * Get the raw image path without asset() for better quality.
     */
    public function getRawImageUrlAttribute()
    {
        return $this->image_path;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($giftImage) {
            // If this is set as featured, unset other featured images for the same gift
            if ($giftImage->is_featured) {
                static::where('gift_id', $giftImage->gift_id)
                    ->where('is_featured', true)
                    ->update(['is_featured' => false]);
            }
        });

        static::updating(function ($giftImage) {
            // If this is set as featured, unset other featured images for the same gift
            if ($giftImage->is_featured && $giftImage->isDirty('is_featured')) {
                static::where('gift_id', $giftImage->gift_id)
                    ->where('id', '!=', $giftImage->id)
                    ->where('is_featured', true)
                    ->update(['is_featured' => false]);
            }
        });

        static::deleting(function ($giftImage) {
            // Delete the actual image file
            if ($giftImage->image_path) {
                $giftImage->deleteImage($giftImage->image_path);
            }
        });
    }
}