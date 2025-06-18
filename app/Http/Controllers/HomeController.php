<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with services and countries data
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
        
        $totalServices = Service::where('status', 'active')->count();
        $totalCountries = Country::count();
        
        return view('home', compact('services', 'countries', 'banners', 'totalServices', 'totalCountries'));
    }

    /**
     * Display the checkout page
     */
    public function checkout(Request $request)
    {
        return view('checkout');
    }

    /**
     * Display all categories page
     */
    public function allCategories()
    {
        return view('all-categories');
    }

    /**
     * Display all gifts page
     */
    public function allGifts()
    {
        return view('all-gifts');
    }

    /**
     * Display individual gift page
     */
    public function showGift(Request $request, $id)
    {
        // Demo gift data - in a real app, this would come from a database
        $gifts = [
            'flowers' => [
                'id' => 'flowers',
                'name' => 'Beautiful Flower Bouquet',
                'price' => 45.99,
                'description' => 'A stunning arrangement of fresh seasonal flowers, carefully selected and beautifully arranged to express your feelings.',
                'images' => [
                    'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1487070183336-b863922373d4?w=800&h=800&fit=crop'
                ]
            ],
            'chocolate' => [
                'id' => 'chocolate',
                'name' => 'Premium Chocolate Box',
                'price' => 29.99,
                'description' => 'Indulge in our exquisite collection of handcrafted chocolates, made with the finest ingredients.',
                'images' => [
                    'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1511381939415-e44015466834?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?w=800&h=800&fit=crop'
                ]
            ],
            'jewelry' => [
                'id' => 'jewelry',
                'name' => 'Elegant Necklace',
                'price' => 89.99,
                'description' => 'A timeless piece of jewelry that adds elegance to any outfit. Crafted with attention to detail.',
                'images' => [
                    'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1506630448388-4e683c67ddb0?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&h=800&fit=crop'
                ]
            ],
            'watch' => [
                'id' => 'watch',
                'name' => 'Luxury Watch',
                'price' => 199.99,
                'description' => 'A sophisticated timepiece that combines style and functionality. Perfect for any occasion.',
                'images' => [
                    'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1434056886845-dac89ffe9b56?w=800&h=800&fit=crop'
                ]
            ],
            'perfume' => [
                'id' => 'perfume',
                'name' => 'Designer Perfume',
                'price' => 75.99,
                'description' => 'A captivating fragrance that leaves a lasting impression. Perfect for special occasions.',
                'images' => [
                    'https://images.unsplash.com/photo-1541643600914-78b084683601?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=800&h=800&fit=crop'
                ]
            ],
            'wine' => [
                'id' => 'wine',
                'name' => 'Premium Wine Bottle',
                'price' => 65.99,
                'description' => 'A fine wine selection perfect for celebrations and special moments.',
                'images' => [
                    'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1547595628-c61a29f496f0?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=800&h=800&fit=crop'
                ]
            ],
            'teddy' => [
                'id' => 'teddy',
                'name' => 'Cute Teddy Bear',
                'price' => 25.99,
                'description' => 'A soft and cuddly teddy bear that brings comfort and joy to anyone who receives it.',
                'images' => [
                    'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1530325553146-0113e5d3a8c2?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=800&fit=crop'
                ]
            ],
            'candles' => [
                'id' => 'candles',
                'name' => 'Scented Candle Set',
                'price' => 35.99,
                'description' => 'Create a relaxing atmosphere with our premium scented candles. Perfect for unwinding.',
                'images' => [
                    'https://images.unsplash.com/photo-1602874801006-e26d405c9c8f?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=800&h=800&fit=crop'
                ]
            ]
        ];

        // Get gift data or return 404
        $gift = $gifts[$id] ?? null;
        
        if (!$gift) {
            abort(404, 'Gift not found');
        }

        return view('gift', compact('gift'));
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