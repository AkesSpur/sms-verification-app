<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProductCategory;
use App\Models\DigitalProductSubcategory;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DigitalProductSubcategoryController extends Controller
{
    use ImageUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DigitalProductSubcategory::with(['category', 'products']);

        // Filter by category if specified
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $subcategories = $query->orderBy('sort_order')
                              ->orderBy('name')
                              ->get();

        return view('admin.digital-product-subcategory.index', compact('subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = DigitalProductCategory::active()->ordered()->get();
        
        return view('admin.digital-product-subcategory.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:digital_product_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_product_subcategories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['category_id', 'name', 'slug', 'description', 'status', 'sort_order']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request, 'image', 'uploads/digital-products/subcategories');
        }

        DigitalProductSubcategory::create($data);

        toastr('Subcategory created successfully!', 'success');
        return redirect()->route('admin.digital-product-subcategories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalProductSubcategory $digitalProductSubcategory)
    {
        return redirect()->route('admin.digital-product-subcategories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalProductSubcategory $digitalProductSubcategory)
    {
        $categories = DigitalProductCategory::active()->ordered()->get();
        return view('admin.digital-product-subcategory.edit', compact('digitalProductSubcategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalProductSubcategory $digitalProductSubcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:digital_product_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:digital_product_subcategories,slug,' . $digitalProductSubcategory->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['category_id', 'name', 'slug', 'description', 'status', 'sort_order']);
        
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
            $data['image'] = $this->updateImage($request, 'image', 'uploads/digital-products/subcategories', $digitalProductSubcategory->image);
        }

        $digitalProductSubcategory->update($data);

        toastr('Subcategory updated successfully!', 'success');
        return redirect()->route('admin.digital-product-subcategories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalProductSubcategory $digitalProductSubcategory)
    {
        // Check if subcategory has products
        if ($digitalProductSubcategory->products()->count() > 0) {
            toastr('Cannot delete subcategory with existing products!', 'error');
            return redirect()->back();
        }

        // Delete image if exists
        if ($digitalProductSubcategory->image) {
            $this->deleteImage($digitalProductSubcategory->image);
        }

        $digitalProductSubcategory->delete();

        toastr('Subcategory deleted successfully!', 'success');
        return redirect()->route('admin.digital-product-subcategories.index');
    }

    /**
     * Get subcategories by category (AJAX).
     */
    public function getByCategory(DigitalProductCategory $category)
    {
        $subcategories = $category->activeSubcategories()->get(['id', 'name']);
        return response()->json($subcategories);
    }
}