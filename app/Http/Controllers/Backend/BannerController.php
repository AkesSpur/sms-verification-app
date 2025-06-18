<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::ordered()->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:10048',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean'
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link_url = $request->link_url;
        $banner->sort_order = $request->sort_order ?? 0;
        $banner->status = $request->status;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $banner->uploadImage($request, 'image', 'uploads/banners');
            $banner->image_path = $imagePath;
        }

        $banner->save();

        toastr()->success('Banner created successfully!');
        return redirect()->route('admin.banners.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean'
        ]);

        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link_url = $request->link_url;
        $banner->sort_order = $request->sort_order ?? 0;
        $banner->status = $request->status;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_path) {
                $banner->deleteImage($banner->image_path);
            }
            
            $imagePath = $banner->uploadImage($request, 'image', 'uploads/banners');
            $banner->image_path = $imagePath;
        }

        $banner->save();

        toastr()->success('Banner updated successfully!');
        return redirect()->route('admin.banners.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        // Delete image file
        if ($banner->image_path) {
            $banner->deleteImage($banner->image_path);
        }

        $banner->delete();

        return response(['status' => 'success', 'message' => 'Banner deleted successfully!']);
    }

    /**
     * Toggle banner status.
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->status = !$banner->status;
        $banner->save();

        $status = $banner->status ? 'activated' : 'deactivated';
        return response()->json([
            'success' => true,
            'message' => "Banner {$status} successfully!",
            'status' => $banner->status
        ]);
    }
}