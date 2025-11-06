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
            Log::info('Starting Owlet services sync (safe refresh)');
            
            // Check if API key is configured
            $apiKey = config('services.owlet.api_key', env('OWLET_API_KEY'));
            if (empty($apiKey)) {
                Log::error('Owlet API key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Owlet API key is not configured. Please set OWLET_API_KEY in your .env file.'
                ]);
            }
            
            $owletService = new OwletApiService();
            $services = $owletService->services();
            
            Log::info('Owlet API response received', ['is_array' => is_array($services), 'service_count' => is_array($services) ? count($services) : 0]);
            
            if (!$services || !is_array($services) || empty($services)) {
                Log::error('Failed to fetch services from Owlet API', ['response' => $services]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch services from Owlet API. Check logs for details.'
                ]);
            }
            
            // Safe refresh: upsert products and categories; don't delete products with orders
            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $archivedCount = 0; // products kept but disabled (had orders)
            $removedCount = 0;  // products deleted (no orders)
            $categoryMap = [];
            $apiServiceIds = [];
            $apiCategoryNames = [];
            
            foreach ($services as $service) {
                try {
                    // Skip if required fields are missing
                    if (empty($service['service']) || empty($service['name']) || empty($service['category'])) {
                        $skippedCount++;
                        continue;
                    }
                    
                    $externalId = (int) $service['service'];
                    $apiServiceIds[] = $externalId;
                    $categoryName = $service['category'];
                    $apiCategoryNames[] = $categoryName;
                    
                    // Handle category - upsert by slug
                    if (!isset($categoryMap[$categoryName])) {
                        $categorySlug = Str::slug($categoryName);
                        $category = SocialMediaCategory::firstOrCreate(
                            ['slug' => $categorySlug],
                            [
                                'name' => $categoryName,
                                'description' => 'Auto-generated category from Owlet API: ' . $categoryName,
                                'status' => true,
                                'sort_order' => 0
                            ]
                        );
                        $categoryMap[$categoryName] = $category->id;
                    }
                    
                    $categoryId = $categoryMap[$categoryName];
                    
                    // Calculate price with 25% markup
                    $originalPrice = (float) ($service['rate'] ?? 0);
                    $markedUpPrice = ceil($originalPrice * 1.25);
                    
                    // Upsert product by external_service_id
                    $product = SocialMediaProduct::firstOrNew(['external_service_id' => $externalId]);
                    $isNew = !$product->exists;
                    
                    $product->category_id = $categoryId;
                    $product->name = $service['name'];
                    $product->slug = Str::slug($service['name'] . '-' . $externalId);
                    $product->description = $this->generateDescription($service);
                    $product->price_per_1000 = $markedUpPrice;
                    $product->min_quantity = (int) ($service['min'] ?? 1);
                    $product->max_quantity = (int) ($service['max'] ?? 1000000);
                    $product->status = true; // re-enable on sync
                    $product->sort_order = 0;
                    
                    $product->save();
                    
                    if ($isNew) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing service: ' . ($service['name'] ?? 'Unknown'), [
                        'error' => $e->getMessage(),
                        'service' => $service
                    ]);
                    $skippedCount++;
                }
            }
            
            // Cleanup products not present in API response
            $apiServiceIds = array_unique(array_map('intval', $apiServiceIds));
            $orphanProducts = SocialMediaProduct::whereNotIn('external_service_id', $apiServiceIds)->get();
            foreach ($orphanProducts as $product) {
                if ($product->orders()->exists()) {
                    // Keep product but disable so it's not selectable for new orders
                    $product->status = false;
                    $product->save();
                    $archivedCount++;
                    Log::info('Archived product with existing orders', ['product_id' => $product->id, 'external_service_id' => $product->external_service_id]);
                } else {
                    // Safe to delete if no orders
                    $product->delete();
                    $removedCount++;
                    Log::info('Removed unused product', ['product_id' => $product->id, 'external_service_id' => $product->external_service_id]);
                }
            }
            
            // Cleanup unused categories (no products) not present in API
            $apiCategorySlugs = array_unique(array_map(function ($name) { return Str::slug($name); }, $apiCategoryNames));
            $unusedCategories = SocialMediaCategory::whereDoesntHave('products')
                ->whereNotIn('slug', $apiCategorySlugs)
                ->get();
            foreach ($unusedCategories as $category) {
                $category->delete();
                Log::info('Removed unused category', ['category_id' => $category->id, 'slug' => $category->slug]);
            }
            
            $categoriesCount = count($categoryMap);
            $message = "Sync completed! Categories: {$categoriesCount}, Created: {$createdCount}, Updated: {$updatedCount}";
            if ($archivedCount > 0) { $message .= ", Archived: {$archivedCount}"; }
            if ($removedCount > 0) { $message .= ", Removed: {$removedCount}"; }
            if ($skippedCount > 0) { $message .= ", Skipped: {$skippedCount}"; }
            
            Log::info('Owlet services sync completed (safe refresh)', [
                'categories_count' => $categoriesCount,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'archived' => $archivedCount,
                'removed' => $removedCount,
                'skipped' => $skippedCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Owlet services sync failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Owlet services sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a description for the product based on service data
     */
    private function generateDescription(array $service): string
    {
        $description = [];
        
        if (!empty($service['type']) && $service['type'] !== 'Default') {
            $description[] = 'Type: ' . $service['type'];
        }
        
        if (isset($service['min']) && isset($service['max'])) {
            $description[] = 'Quantity: ' . number_format($service['min']) . ' - ' . number_format($service['max']);
        }
        
        if (isset($service['dripfeed']) && $service['dripfeed']) {
            $description[] = 'Supports dripfeed delivery';
        }
        
        if (isset($service['refill']) && $service['refill']) {
            $description[] = 'Refillable service';
        }
        
        if (isset($service['cancel']) && $service['cancel']) {
            $description[] = 'Cancellable';
        }
        
        $baseDescription = 'Social media boosting service from Owlet API.';
        
        if (!empty($description)) {
            return $baseDescription . ' ' . implode('. ', $description) . '.';
        }
        
        return $baseDescription;
    }

    /**
     * Test Owlet API connection
     */
    public function testOwletConnection()
    {
        try {
            $apiKey = config('services.owlet.api_key', env('OWLET_API_KEY'));
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key not configured'
                ]);
            }

            $owletService = new OwletApiService();
            $balance = $owletService->balance();
            
            Log::info('Owlet API connection test', ['response' => $balance]);
            
            return response()->json([
                'success' => true,
                'message' => 'API connection successful',
                'data' => $balance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Owlet API connection test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ]);
        }
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