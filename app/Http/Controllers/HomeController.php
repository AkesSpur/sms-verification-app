<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Gift;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\DigitalProductCategory;
use App\Models\DigitalProductSubcategory;
use App\Models\DigitalProduct;

class HomeController extends Controller
{
    /**
     * Display the home page with services, countries, and gifts data
     */
    public function index()
    {
        $services = Service::where('status', 'active')
                          ->orderBy('name')
                          ->take(6)
                          ->get();
        
        $countries = Country::orderBy('name')
                           ->take(8)
                           ->get();
        
        $banners = Banner::active()->ordered()->get();
        
        // Get featured gifts for the home page
        $gifts = Gift::where('status', true)
                      ->orderBy('sort_order')
                      ->take(8)
                      ->get();
        
        // Get digital product categories with their subcategories and products
        $digitalCategories = DigitalProductCategory::active()
                                                  ->ordered()
                                                  ->with(['activeSubcategories' => function($query) {
                                                      $query->whereHas('activeProducts');
                                                  }, 'activeSubcategories.activeProducts'])
                                                  ->whereHas('activeSubcategories', function($query) {
                                                      $query->whereHas('activeProducts');
                                                  })
                                                  ->take(8)
                                                  ->get();
        
        // Get all active subcategories with products (limit to 8 for home page)
        // Only include subcategories whose parent category is also active
        $activeSubcategories = DigitalProductSubcategory::active()
                                                        ->whereHas('activeProducts')
                                                        ->whereHas('category', function($query) {
                                                            $query->where('status', 1);
                                                        })
                                                        ->with(['activeProducts', 'category'])
                                                        ->ordered()
                                                        ->take(8)
                                                        ->get();
        
        // Transform data for JavaScript consumption
        $digitalProductsData = $activeSubcategories->map(function($subcategory) {
            return [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'image' => $subcategory->image,
                'category' => $subcategory->category->name,
                'products' => $subcategory->activeProducts->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'stock' => $product->available_stock,
                        'image' => $product->image
                    ];
                })->toArray()
            ];
        });
        
        $totalServices = Service::where('status', 'active')->count();
        $totalCountries = Country::count();
        
        return view('home', compact('services',
         'countries',
                     'banners',
                      'gifts',
                     'digitalCategories',
                     'activeSubcategories',
                      'digitalProductsData',
                       'totalServices',
                    'totalCountries'
                    ));
    }

    /**
     * Display the checkout page
     */
    public function checkout(Request $request)
    {
        return view('checkout');
    }

    public function showProduct($slug)
    {
        $product = DigitalProduct::where('slug', $slug)
                                ->where('status', true)
                                ->with(['subcategory.category'])
                                ->firstOrFail();
        
        return view('checkout', compact('product'));
    }

    /**
     * Display all categories page
     */
    public function allCategories()
    {
        $banners = Banner::active()->ordered()->get();
        
        // Get all digital product categories with their subcategories and products
        $digitalCategories = DigitalProductCategory::active()
                                                  ->ordered()
                                                  ->with(['activeSubcategories' => function($query) {
                                                      $query->whereHas('activeProducts');
                                                  }, 'activeSubcategories.activeProducts'])
                                                  ->whereHas('activeSubcategories', function($query) {
                                                      $query->whereHas('activeProducts');
                                                  })
                                                  ->get();
        
        // Transform data for JavaScript consumption
        $digitalProductsData = $digitalCategories->flatMap(function($category) {
            return $category->activeSubcategories->filter(function($subcategory) {
                return $subcategory->activeProducts->count() > 0;
            })->map(function($subcategory) {
                return [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'image' => $subcategory->image,
                    'category' => $subcategory->category->name,
                    'products' => $subcategory->activeProducts->map(function($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'price' => $product->price,
                            'stock' => $product->available_stock,
                            'image' => $product->image
                        ];
                    })->toArray()
                ];
            })->toArray();
        });
        
        return view('all-categories', compact('digitalCategories', 'digitalProductsData', 'banners'));
    }

    /**
     * Display all gifts page
     */
    public function allGifts()
    {
        $gifts = Gift::where('status', true)
                      ->orderBy('sort_order')
                      ->get();
                      
        return view('all-gifts', compact('gifts'));
    }

    /**
     * Display individual gift page
     */
    public function showGift(Request $request, $slug)
    {
        // Find the gift by slug
        $gift = Gift::where('slug', $slug)
                      ->where('status', true)
                      ->with('images')
                      ->firstOrFail();
                      

        return view('gift', ['gift' => $gift]);
    }

    /**
     * Handle gift order
     */
    public function orderGift(Request $request)
    {
        // Validate the request
        $request->validate([
            'gift_id' => 'required|string',
            'gift_name' => 'required|string',
            'gift_price' => 'required|numeric',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'sender_email' => 'required|email|max:255',
            'delivery_address' => 'required|string|max:500',
            'delivery_city' => 'required|string|max:100',
            'delivery_state' => 'required|string|max:100',
            'delivery_country' => 'required|string|max:100',
            'delivery_zip' => 'required|string|max:20',
            'customize_gift' => 'boolean',
            'custom_message' => 'nullable|string|max:500',
            'custom_image' => 'nullable|image|max:2048'
        ]);

        // Handle file upload if present
        $customImagePath = null;
        if ($request->hasFile('custom_image')) {
            $customImagePath = $request->file('custom_image')->store('gift-customizations', 'public');
        }

        // In a real application, you would save this to the database
        // For now, we'll just redirect to a success page or back with success message
        
        return redirect()->route('home')->with('success', 'Your gift order has been placed successfully! We will contact you shortly with delivery details.');
    }
}