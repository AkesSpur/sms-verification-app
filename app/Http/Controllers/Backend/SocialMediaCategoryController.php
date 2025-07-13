<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SocialMediaCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = SocialMediaCategory::with('products')
        ->orderBy('sort_order')->paginate(20);
        return view('admin.social-media-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.social-media-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:social_media_categories,name',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0'
        ]);

        $category = SocialMediaCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
            'sort_order' => $request->sort_order
        ]);

        toastr('Social Media Category created successfully!', 'success');
        return redirect()->route('admin.social-media-categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialMediaCategory $socialMediaCategory)
    {
        return view('admin.social-media-categories.show', compact('socialMediaCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialMediaCategory $socialMediaCategory)
    {
        return view('admin.social-media-categories.edit', compact('socialMediaCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialMediaCategory $socialMediaCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:social_media_categories,name,' . $socialMediaCategory->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0'
        ]);

        $socialMediaCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
            'sort_order' => $request->sort_order
        ]);

        toastr('Social Media Category updated successfully!', 'success');
        return redirect()->route('admin.social-media-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMediaCategory $socialMediaCategory)
    {
        // Check if category has products
        if ($socialMediaCategory->products()->count() > 0) {
            toastr('Cannot delete category with existing products!', 'error');
            return redirect()->back();
        }

        $socialMediaCategory->delete();
        toastr('Social Media Category deleted successfully!', 'success');
        return redirect()->route('admin.social-media-categories.index');
    }
}