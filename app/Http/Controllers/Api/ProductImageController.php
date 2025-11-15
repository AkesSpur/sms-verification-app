<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DigitalProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    /**
     * Return image URLs for active digital products.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 0);

        $query = DigitalProduct::query()
            ->select(['id', 'name', 'slug', 'image', 'status', 'sort_order'])
            ->where('status', 1)
            ->orderBy('sort_order');

        $products = $limit > 0 ? $query->limit($limit)->get() : $query->get();

        $items = $products->map(function ($p) {
            $image = $p->image;
            $imageUrl = null;

            if ($image) {
                $normalized = ltrim($image, '/');
                if (Str::startsWith($normalized, ['http://', 'https://'])) {
                    $imageUrl = $normalized;
                } else {
                    // If the stored image already includes uploads/digital-products, respect it; otherwise prefix
                    if (Str::startsWith($normalized, 'uploads/digital-products/')) {
                        $imageUrl = url($normalized);
                    } else {
                        $imageUrl = url('uploads/digital-products/' . $normalized);
                    }
                }
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'image_url' => $imageUrl,
            ];
        });

        return response()->json([
            'count' => $items->count(),
            'items' => $items,
        ])->withHeaders([
            // Basic CORS headers so Framer can fetch cross-origin
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type',
        ]);
    }
}