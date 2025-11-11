<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResellerProduct;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResellerProductController extends Controller
{
    use ImageUploadTrait;
    /**
     * Display a listing of reseller products.
     */
    public function index(Request $request)
    {
        $query = ResellerProduct::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('sort_order')->paginate(20);
        return view('admin.reseller-products.index', compact('products'));
    }

    /**
     * Show the form for creating a new reseller product.
     */
    public function create()
    {
        return view('admin.reseller-products.create');
    }

    /**
     * Store a newly created reseller product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:reseller_products,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'price', 'status', 'sort_order']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $data['stock'] = 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request, 'image', 'uploads/reseller-products/products');
        }

        $product = ResellerProduct::create($data);
        toastr('Reseller product created successfully!', 'success');
        return redirect()->route('admin.reseller-products.index');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(ResellerProduct $resellerProduct)
    {
        return view('admin.reseller-products.edit', compact('resellerProduct'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, ResellerProduct $resellerProduct)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:reseller_products,slug,' . $resellerProduct->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'price', 'status', 'sort_order']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        // Handle image upload/update
        if ($request->hasFile('image')) {
            $data['image'] = $this->updateImage($request, 'image', 'uploads/reseller-products/products', $resellerProduct->image);
        }

        $resellerProduct->update($data);
        $resellerProduct->updateStock();

        toastr('Reseller product updated successfully!', 'success');
        return redirect()->route('admin.reseller-products.index');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(ResellerProduct $resellerProduct)
    {
        if ($resellerProduct->logs()->count() > 0) {
            toastr('Cannot delete product with existing logs!', 'error');
            return redirect()->back();
        }

        $resellerProduct->delete();
        toastr('Reseller product deleted successfully!', 'success');
        return redirect()->route('admin.reseller-products.index');
    }
}