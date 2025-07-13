<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaCategory;
use App\Models\SocialMediaProduct;
use Illuminate\Http\Request;
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
}