<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, ImageUploadTrait;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link_url',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the full URL for the banner image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }
        return null;
    }

    /**
     * Scope to get only active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($banner) {
            // Delete the actual image file
            if ($banner->image_path) {
                $banner->deleteImage($banner->image_path);
            }
        });
    }
}