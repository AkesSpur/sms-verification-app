<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProduct;
use App\Models\DigitalProductCategory;
use App\Models\DigitalProductSubcategory;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DigitalProductController extends Controller
{
    use ImageUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = DigitalProduct::with(['subcategory.category'])
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();

        return view('admin.digital-product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = DigitalProductCategory::active()->ordered()->get();
        $subcategories = DigitalProductSubcategory::active()->ordered()->get();
        return view('admin.digital-product.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:digital_product_subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_products,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['subcategory_id', 'name', 'slug', 'description', 'price', 'status', 'sort_order']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        // Set default stock to 0
        $data['stock'] = 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request, 'image', 'uploads/digital-products/products');
        }

        $product = DigitalProduct::create($data);
        
        // Update stock based on available logs
        $product->updateStock();

        toastr('Product created successfully!', 'success');
        return redirect()->route('admin.digital-products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalProduct $digitalProduct)
    {
        $digitalProduct->load(['subcategory.category', 'logs']);
        return view('admin.digital-product.show', compact('digitalProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalProduct $digitalProduct)
    {
        $categories = DigitalProductCategory::active()->ordered()->get();
        $subcategories = DigitalProductSubcategory::active()->ordered()->get();
        return view('admin.digital-product.edit', compact('digitalProduct', 'categories', 'subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalProduct $digitalProduct)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:digital_product_subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_products,slug,' . $digitalProduct->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['subcategory_id', 'name', 'slug', 'description', 'price', 'status', 'sort_order']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        // Handle image upload/update
        if ($request->hasFile('image')) {
            $data['image'] = $this->updateImage($request, 'image', 'uploads/digital-products/products', $digitalProduct->image);
        }

        $digitalProduct->update($data);
        
        // Update stock based on available logs
        $digitalProduct->updateStock();

        toastr('Product updated successfully!', 'success');
        return redirect()->route('admin.digital-products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalProduct $digitalProduct)
    {
        // Check if product has logs
        if ($digitalProduct->logs()->count() > 0) {
            toastr('Cannot delete product with existing logs!', 'error');
            return redirect()->back();
        }

        // Delete image if exists
        if ($digitalProduct->image) {
            $this->deleteImage($digitalProduct->image);
        }

        $digitalProduct->delete();

        toastr('Product deleted successfully!', 'success');
        return redirect()->route('admin.digital-products.index');
    }
}