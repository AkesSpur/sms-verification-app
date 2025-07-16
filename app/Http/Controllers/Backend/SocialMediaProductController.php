<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaCategory;
use App\Models\SocialMediaProduct;
use App\Services\OwletApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialMediaProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SocialMediaProduct::with('category');

        // Filter by category (support both 'category' and 'category_id' parameters)
        if ($request->filled('category') || $request->filled('category_id')) {
            $categoryId = $request->category ?? $request->category_id;
            $query->where('category_id', $categoryId);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $products = $query->orderBy('sort_order')->paginate(20);
        $categories = SocialMediaCategory::active()->ordered()->get();

        return view('admin.social-media-products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = SocialMediaCategory::active()->ordered()->get();
        return view('admin.social-media-products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:social_media_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_1000' => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'required|integer|min:1|gte:min_quantity',
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0'
        ]);

        $product = SocialMediaProduct::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price_per_1000' => $request->price_per_1000,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'status' => $request->status,
            'sort_order' => $request->sort_order
        ]);

        toastr('Social Media Product created successfully!', 'success');
        return redirect()->route('admin.social-media-products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialMediaProduct $socialMediaProduct)
    {
        $socialMediaProduct->load('category', 'orders');
        return view('admin.social-media-products.show', compact('socialMediaProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialMediaProduct $socialMediaProduct)
    {
        $categories = SocialMediaCategory::active()->ordered()->get();
        return view('admin.social-media-products.edit', compact('socialMediaProduct', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialMediaProduct $socialMediaProduct)
    {
        $request->validate([
            'category_id' => 'required|exists:social_media_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_1000' => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'required|integer|min:1|gte:min_quantity',
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0'
        ]);

        $socialMediaProduct->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price_per_1000' => $request->price_per_1000,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'status' => $request->status,
            'sort_order' => $request->sort_order
        ]);

        toastr('Social Media Product updated successfully!', 'success');
        return redirect()->route('admin.social-media-products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMediaProduct $socialMediaProduct)
    {
        // Check if product has orders
        if ($socialMediaProduct->orders()->count() > 0) {
            toastr('Cannot delete product with existing orders!', 'error');
            return redirect()->back();
        }

        $socialMediaProduct->delete();
        toastr('Social Media Product deleted successfully!', 'success');
        return redirect()->route('admin.social-media-products.index');
    }

    /**
     * Sync services from Owlet API
     */
    public function syncOwletServices()
    {
        try {
            $owletService = new OwletApiService();
            $services = $owletService->services();
            
            if (!$services || !isset($services['services'])) {
                toastr('Failed to fetch services from Owlet API', 'error');
                return redirect()->back();
            }
            
            $syncedCount = 0;
            $skippedCount = 0;
            
            foreach ($services['services'] as $service) {
                // Check if product already exists with this external service ID
                $existingProduct = SocialMediaProduct::where('external_service_id', $service['service'])->first();
                
                if ($existingProduct) {
                    $skippedCount++;
                    continue;
                }
                
                // Try to find a suitable category or use the first one
                $category = SocialMediaCategory::active()->first();
                
                if (!$category) {
                    Log::warning('No active social media category found for Owlet service sync');
                    continue;
                }
                
                // Create new product
                SocialMediaProduct::create([
                    'category_id' => $category->id,
                    'name' => $service['name'],
                    'description' => $service['name'] . ' - Synced from Owlet API',
                    'price_per_1000' => $service['rate'],
                    'min_quantity' => $service['min'],
                    'max_quantity' => $service['max'],
                    'status' => true,
                    'sort_order' => 0,
                    'external_service_id' => $service['service']
                ]);
                
                $syncedCount++;
            }
            
            $message = "Synced {$syncedCount} new services";
            if ($skippedCount > 0) {
                $message .= ", skipped {$skippedCount} existing services";
            }
            
            toastr($message, 'success');
            
        } catch (\Exception $e) {
            Log::error('Owlet services sync failed: ' . $e->getMessage());
            toastr('Failed to sync services: ' . $e->getMessage(), 'error');
        }
        
        return redirect()->route('admin.social-media-products.index');
    }

    /**
     * Get products by category (AJAX)
     */
    public function getByCategory($categoryId)
    {
        $products = SocialMediaProduct::where('category_id', $categoryId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'price_per_1000', 'min_quantity', 'max_quantity', 'description']);

        return response()->json($products);
    }

    /**
     * Bulk update prices
     */
    public function bulkUpdatePrices(Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:social_media_products,id'
        ]);

        try {
            $actionType = $request->action_type;
            $value = $request->value;
            $productIds = $request->product_ids;

            $products = SocialMediaProduct::whereIn('id', $productIds)->get();
            $updatedCount = 0;

            foreach ($products as $product) {
                $oldPrice = $product->price_per_1000;
                
                if ($actionType === 'percentage') {
                    // Increase by percentage
                    $newPrice = $oldPrice * (1 + ($value / 100));
                } else {
                    // Set fixed price
                    $newPrice = $value;
                }

                $product->update(['price_per_1000' => round($newPrice, 2)]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated prices for {$updatedCount} products.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update prices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:0,1',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:social_media_products,id'
        ]);

        try {
            $status = $request->status;
            $productIds = $request->product_ids;

            $updatedCount = SocialMediaProduct::whereIn('id', $productIds)
                ->update(['status' => $status]);

            $statusText = $status == 1 ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Successfully {$statusText} {$updatedCount} products.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}