<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProductCategory;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DigitalProductCategoryController extends Controller
{
    use ImageUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DigitalProductCategory::with('subcategories')
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();

        return view('admin.digital-product-category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.digital-product-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:digital_product_categories,name',
            'slug' => 'nullable|string|max:255|unique:digital_product_categories,slug',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'status', 'sort_order']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        DigitalProductCategory::create($data);

        toastr('Category created successfully!', 'success');
        return redirect()->route('admin.digital-product-categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalProductCategory $digitalProductCategory)
    {
        return redirect()->route('admin.digital-product-categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalProductCategory $digitalProductCategory)
    {
        return view('admin.digital-product-category.edit', compact('digitalProductCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigitalProductCategory $digitalProductCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:digital_product_categories,name,' . $digitalProductCategory->id,
            'slug' => 'nullable|string|max:255|unique:digital_product_categories,slug,' . $digitalProductCategory->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'status', 'sort_order']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        $digitalProductCategory->update($data);

        toastr('Category updated successfully!', 'success');
        return redirect()->route('admin.digital-product-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalProductCategory $digitalProductCategory)
    {
        // Check if category has subcategories
        if ($digitalProductCategory->subcategories()->count() > 0) {
            toastr('Cannot delete category with existing subcategories!', 'error');
            return redirect()->back();
        }

        $digitalProductCategory->delete();

        toastr('Category deleted successfully!', 'success');
        return redirect()->route('admin.digital-product-categories.index');
    }
}