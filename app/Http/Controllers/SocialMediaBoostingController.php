<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaCategory;
use App\Models\SocialMediaProduct;
use App\Models\SocialMediaOrder;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SaleNotificationMail;

class SocialMediaBoostingController extends Controller
{
    /**
     * Display social media boosting categories and products in a single page.
     */
    public function index()
    {
        $categories = SocialMediaCategory::active()->ordered()->with('activeProducts')->get();
        
        // Calculate uncompleted orders count (pending + processing)
        $uncompletedOrdersCount = SocialMediaOrder::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'processing'])
            ->count();
        
        return view('user.social-media-boosting.index', compact('categories', 'uncompletedOrdersCount'));
    }

    /**
     * Display products for a specific category.
     */
    public function category($slug)
    {
        $category = SocialMediaCategory::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $products = $category->activeProducts()->get();

        return view('user.social-media-boosting.category', compact('category', 'products'));
    }

    /**
     * Display product details and purchase form.
     */
    public function product($categorySlug, $productSlug)
    {
        $category = SocialMediaCategory::where('slug', $categorySlug)
            ->where('status', 1)
            ->firstOrFail();

        $product = SocialMediaProduct::where('slug', $productSlug)
            ->where('category_id', $category->id)
            ->where('status', 1)
            ->firstOrFail();

        return view('user.social-media-boosting.product', compact('category', 'product'));
    }

    /**
     * Calculate price based on quantity (AJAX).
     */
    public function calculatePrice(Request $request, SocialMediaProduct $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:' . $product->min_quantity . '|max:' . $product->max_quantity
        ]);

        $quantity = $request->quantity;
        $totalPrice = $product->calculatePrice($quantity);

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'unit_price' => $product->price_per_1000,
            'total_price' => $totalPrice,
            'formatted_total_price' => '₦' . number_format($totalPrice, 2)
        ]);
    }

    /**
     * Process the purchase.
     */
    public function purchase(Request $request, SocialMediaProduct $product)
    {
        $request->validate([
            'social_media_link' => 'required|url',
            'quantity' => 'required|integer|min:' . $product->min_quantity . '|max:' . $product->max_quantity
        ]);

        $user = Auth::user();
        $quantity = $request->quantity;
        $unitPrice = $product->price_per_1000;
        $totalAmount = $product->calculatePrice($quantity);

        // Check if user has sufficient balance
        if ($user->balance < $totalAmount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance. Please top up your wallet.');
        }

        DB::beginTransaction();

        try {
            // Create the order
            $order = SocialMediaOrder::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'social_media_link' => $request->social_media_link,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'wallet',
                'payment_status' => 'paid'
            ]);

            // Deduct amount from user's wallet
            $user->decrement('balance', $totalAmount);

            // Create transaction record
            Transaction::createTransaction(
                $user,
                'debit',
                'social_media_purchase',
                $totalAmount,
                "Social Media Boosting Purchase - {$product->name}",
                ['order_id' => $order->id, 'product_id' => $product->id],
                $order
            );

            // Send email notification to admin
            $settings = GeneralSetting::first();
            if ($settings && $settings->contact_email) {
                $saleData = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'product_name' => $product->name,
                    'category' => $product->category->name ?? 'Social Media Boosting',
                    'quantity' => $quantity,
                    'customer_name' => $user->name,
                    'social_media_link' => $request->social_media_link,
                    'price' => $totalAmount
                ];

                try {
                    Mail::to($settings->contact_email)->queue(
                        new SaleNotificationMail('social_media', $saleData, $totalAmount)
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send social media sale notification email: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('user.social-media-orders.index')
                ->with('success', 'Order placed successfully! Your order is now pending and will be processed by our team.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Social Media Order Creation Failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to process your order. Please try again.');
        }
    }

    /**
     * Display user's social media orders.
     */
    public function orders(Request $request)
    {
        $userId = Auth::id();
        
        // Calculate statistics
        $totalOrders = SocialMediaOrder::where('user_id', $userId)->count();
        $pendingCount = SocialMediaOrder::where('user_id', $userId)->where('status', 'pending')->count();
        $processingCount = SocialMediaOrder::where('user_id', $userId)->where('status', 'processing')->count();
        $completedCount = SocialMediaOrder::where('user_id', $userId)->where('status', 'completed')->count();
        $cancelledCount = SocialMediaOrder::where('user_id', $userId)->where('status', 'cancelled')->count();
        
        $query = SocialMediaOrder::where('user_id', $userId)
            ->with(['product.category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('user.social-media-boosting.orders', compact(
            'orders', 
            'totalOrders', 
            'pendingCount', 
            'processingCount', 
            'completedCount', 
            'cancelledCount'
        ));
    }

    /**
     * Display specific order details.
     */
    public function showOrder(SocialMediaOrder $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load(['product.category']);
        return view('user.social-media-boosting.order-details', compact('order'));
    }
}