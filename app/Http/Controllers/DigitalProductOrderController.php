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
                $orders = [];
                
                // Create individual orders for each quantity
                for ($i = 0; $i < $quantity; $i++) {
                    // Get an available log
                    $availableLog = $product->availableLogs()->lockForUpdate()->first();
                    
                    if (!$availableLog) {
                        throw new \Exception('No available items in stock.');
                    }

                    // Create order
                    $order = DigitalProductOrder::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'log_id' => $availableLog->id,
                        'quantity' => 1,
                        'unit_price' => $unitPrice,
                        'total_amount' => $unitPrice,
                        'status' => 'pending',
                        'payment_method' => 'wallet',
                        'payment_status' => 'pending'
                    ]);

                    // Mark log as sold
                    $availableLog->markAsSold($user->id);
                    
                    $orders[] = $order;
                }

                // Deduct balance from user with transaction logging
                $user->deductBalance(
                    $totalAmount,
                    'digital_purchase',
                    "Digital product purchase: {$product->name} (Qty: {$quantity})",
                    collect($orders)->first() // Use first order as reference
                );

                // Mark all orders as completed
                foreach ($orders as $order) {
                    $order->markAsCompleted();
                }

                // Update product stock
                $product->updateStock();

                DB::commit();

                // Log the successful purchase
                Log::info('Digital product purchase completed', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_amount' => $totalAmount,
                    'order_ids' => collect($orders)->pluck('id')->toArray()
                ]);

                // Send sales notification email
                try {
                    $emailConfig = EmailConfiguration::first();
                    if ($emailConfig && $emailConfig->email) {
                        $saleData = collect($orders)->map(function ($order) use ($user, $product) {
                            return [
                                'order_id' => $order->id,
                                'category' => $product->subcategory->category->name ?? 'N/A',
                                'name' => $product->name,
                                'quantity' => $order->quantity,
                                'customer_name' => $user->name,
                                'price' => $order->total_amount
                            ];
                        })->toArray();

                        // Calculate total amount from all orders
                        $amount = collect($orders)->sum('total_amount');

                        $settings = GeneralSetting::first();

                        Mail::to($settings->contact_email)->queue(
                            new SaleNotificationMail('digital_product', $saleData, $amount, $settings->site_name ?? 'Admin')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send sales notification email', [
                        'error' => $e->getMessage(),
                        'order_ids' => collect($orders)->pluck('id')->toArray()
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Purchase completed successfully!',
                    'data' => [
                        'orders' => $orders,
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

            $query = $user->digitalProductOrders()
                          ->with(['product.subcategory.category', 'log'])
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
                          ->with(['product.subcategory.category', 'log'])
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