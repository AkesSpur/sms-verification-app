<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\GiftImage;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GiftController extends Controller
{
    use ImageUploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gifts = Gift::with('images')->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return view('admin.gifts.index', compact('gifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:gifts,slug',
            'price' => 'required|numeric|min:0',
            'customizable' => 'boolean',
            'customization_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->all();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Gift::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request, 'featured_image', 'uploads/gifts');
        }

        // Set default values
        $data['customizable'] = (bool) $request->input('customizable', 0);
        $data['status'] = (bool) $request->input('status', 1);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Clear customization cost if not customizable
        if (!$data['customizable']) {
            $data['customization_cost'] = null;
        }

        $gift = Gift::create($data);

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = $this->uploadMultiImage($request, 'gallery_images', 'uploads/gifts/gallery');
            foreach ($galleryPaths as $index => $imagePath) {
                GiftImage::create([
                    'gift_id' => $gift->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index,
                    'is_featured' => $index == 0 && !$gift->featured_image
                ]);
            }
        }

        toastr()->success('Gift created successfully!');
        return redirect()->route('admin.gifts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gift $gift)
    {
        $gift->load('images');
        return view('admin.gifts.show', compact('gift'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gift $gift)
    {
        $gift->load('images');
        return view('admin.gifts.edit', compact('gift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gift $gift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:gifts,slug,' . $gift->id,
            'price' => 'required|numeric|min:0',
            'customizable' => 'boolean',
            'customization_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->all();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug (excluding current gift)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Gift::where('slug', $data['slug'])->where('id', '!=', $gift->id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->updateImage($request, 'featured_image', 'uploads/gifts', $gift->featured_image);
        }

        // Set default values
        $data['customizable'] = (bool) $request->input('customizable', 0);
        $data['status'] = (bool) $request->input('status', 1);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Clear customization cost if not customizable
        if (!$data['customizable']) {
            $data['customization_cost'] = null;
        }

        $gift->update($data);

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            $existingImagesCount = $gift->images()->count();
            $galleryPaths = $this->uploadMultiImage($request, 'gallery_images', 'uploads/gifts/gallery');
            foreach ($galleryPaths as $index => $imagePath) {
                GiftImage::create([
                    'gift_id' => $gift->id,
                    'image_path' => $imagePath,
                    'sort_order' => $existingImagesCount + $index,
                    'is_featured' => false
                ]);
            }
        }

        toastr()->success('Gift updated successfully!');
        return redirect()->route('admin.gifts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gift $gift)
    {
        // Delete featured image
        if ($gift->featured_image) {
            $gift->deleteImage($gift->featured_image);
        }

        // Delete gallery images (trigger model events to delete files)
        foreach ($gift->images as $image) {
            $image->delete();
        }

        $gift->delete();

        return response(['status' => 'success', 'message' => 'Gift deleted successfully!']);
    }

    /**
     * Delete a specific gallery image.
     */
    public function deleteImage(GiftImage $image)
    {
        $image->delete();
        toastr()->success('Image deleted successfully!');
        return response()->json(['success' => true]);
    }

    /**
     * Set an image as featured.
     */
    public function setFeaturedImage(GiftImage $image)
    {
        // Unset other featured images for this gift
        GiftImage::where('gift_id', $image->gift_id)
            ->where('is_featured', true)
            ->update(['is_featured' => false]);

        // Set this image as featured
        $image->update(['is_featured' => true]);

        toastr()->success('Featured image updated successfully!');
        return response()->json(['success' => true]);
    }

    /**
     * Unset featured image.
     */
    public function unsetFeaturedImage($imageId)
    {
        $image = GiftImage::findOrFail($imageId);
        
        // Remove featured status from this image
        $image->update(['is_featured' => false]);

        toastr()->success('Featured status removed successfully!');
        return response()->json(['success' => true]);
    }
}