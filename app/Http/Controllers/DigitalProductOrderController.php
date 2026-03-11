<?php

namespace App\Http\Controllers;

use App\Models\DigitalProduct;
use App\Models\DigitalProductLog;
use App\Models\DigitalProductOrder;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\EmailConfiguration;
use App\Mail\SaleNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class DigitalProductOrderController extends Controller
{
    /**
     * Create a new order for digital product.
     */
    public function store(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to make a purchase.'
                ], 401);
            }

            // Validate request
            $validated = $request->validate([
                'product_id' => 'required|exists:digital_products,id',
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $user = Auth::user();
            $key = 'digital_purchase:' . $user->id;
            if (RateLimiter::tooManyAttempts($key, 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait a moment before submitting another purchase.'
                ], 429);
            }
            RateLimiter::hit($key, 3);
            $product = DigitalProduct::with('availableLogs')->findOrFail($validated['product_id']);
            $quantity = $validated['quantity'];

            // Check if user is active
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not active. Please contact support.'
                ], 403);
            }

            // Check if product is active
            if (!$product->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is currently unavailable.'
                ], 400);
            }

            // Check stock availability
            if ($product->available_stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->available_stock} items available in stock."
                ], 400);
            }

            // Calculate total amount
            $unitPrice = $product->price;
            $totalAmount = $unitPrice * $quantity;

            // Check user balance
            if (!$user->hasSufficientBalance($totalAmount)) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient balance. You need ₦" . number_format($totalAmount - $user->balance) . " more to complete this purchase."
                ], 400);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Fetch available logs for the quantity
                $availableLogs = $product->availableLogs()
                    ->lockForUpdate()
                    ->take($quantity)
                    ->get();

                if ($availableLogs->count() < $quantity) {
                    throw new \Exception('Not enough items in stock to fulfill this order.');
                }

                // Create a single order for the batch
                $order = DigitalProductOrder::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'log_id' => null, // New logic: logs are linked via order_id in logs table
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => 'wallet',
                    'payment_status' => 'pending'
                ]);

                // Mark logs as sold and attach to order
                foreach ($availableLogs as $log) {
                    $log->markAsSold($user->id, $order->id);
                }

                // Deduct balance from user with transaction logging
                $user->deductBalance(
                    $totalAmount,
                    'digital_purchase',
                    "Digital product purchase: {$product->name} (Qty: {$quantity})",
                    $order
                );

                // Mark order as completed
                $order->markAsCompleted();

                // Update product stock (technically markAsSold handles this individually, but we might want to optimize or ensure consistency)
                // Since markAsSold calls updateStock each time, it might be redundant but safe. 
                // Alternatively, we could optimize by calling updateStock once at the end if we modified markAsSold, 
                // but for now let's rely on the existing model method to ensure stock count is correct.
                // However, markAsSold updates the count by counting available logs. So calling it multiple times is fine.

                DB::commit();

                // Log the successful purchase
                Log::info('Digital product purchase completed', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_amount' => $totalAmount,
                    'order_id' => $order->id
                ]);

                // Send sales notification email
                try {
                    $emailConfig = EmailConfiguration::first();
                    $settings    = GeneralSetting::first();
                    $recipient   = $settings->contact_email ?? null;

                    if ($emailConfig && $emailConfig->email && $recipient) {
                        $saleData = [
                            [
                                'order_id'      => $order->id,
                                'category'      => $product->subcategory->category->name ?? 'N/A',
                                'name'          => $product->name,
                                'quantity'      => $order->quantity,
                                'customer_name' => $user->name,
                                'price'         => $order->total_amount
                            ]
                        ];

                        Mail::to($recipient)->queue(
                            new SaleNotificationMail('digital_product', $saleData, $totalAmount, $settings->site_name ?? 'Admin')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send sales notification email', [
                        'error'     => $e->getMessage(),
                        'order_id' => $order->id
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Purchase completed successfully!',
                    'data' => [
                        'orders' => [$order], // Keep structure somewhat consistent for frontend if it expects array
                        'total_amount' => $totalAmount,
                        'remaining_balance' => $user->fresh()->balance,
                        'product_name' => $product->name
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('Digital product purchase failed', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Purchase failed: ' . $e->getMessage()
                ], 500);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error in digital product purchase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get user's digital product orders.
     */
    public function getUserOrders(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view orders.'
                ], 401);
            }

            $user = Auth::user();
            $perPage = $request->get('per_page', 150);
            $status = $request->get('status');
            $search = $request->get('search');

            // Updated query to include 'logs' relationship for new batch orders
            $query = $user->digitalProductOrders()
                          ->with(['product.subcategory.category', 'log', 'logs'])
                          ->orderBy('created_at', 'desc');

            // Filter by status
            if ($status && in_array($status, ['pending', 'completed', 'failed', 'cancelled'])) {
                $query->where('status', $status);
            }

            // Search functionality
            if ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('order_number', 'like', "%{$search}%");
            }

            $orders = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user digital product orders', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.'
            ], 500);
        }
    }

    /**
     * Get specific order details.
     */
    public function show($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view order details.'
                ], 401);
            }

            $user = Auth::user();
            $order = $user->digitalProductOrders()
                          ->with(['product.subcategory.category', 'log', 'logs'])
                          ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
    }
}
