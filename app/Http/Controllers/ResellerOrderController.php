<?php

namespace App\Http\Controllers;

use App\Models\ResellerProduct;
use App\Models\ResellerProductLog;
use App\Models\ResellerOrder;
use App\Models\Transaction;
use App\Models\EmailConfiguration;
use App\Models\GeneralSetting;
use App\Mail\SaleNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ResellerOrderController extends Controller
{
    /**
     * Create a new reseller order.
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to make a purchase.'
                ], 401);
            }

            $validated = $request->validate([
                'product_id' => 'required|exists:reseller_products,id',
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            $user = Auth::user();
            $key = 'reseller_purchase:' . $user->id;
            if (RateLimiter::tooManyAttempts($key, 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait a moment before submitting another purchase.'
                ], 429);
            }
            RateLimiter::hit($key, 3);
            if (!$user->isReseller()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be a reseller to purchase these products.'
                ], 403);
            }

            $product = ResellerProduct::with('availableLogs')->findOrFail($validated['product_id']);
            $quantity = $validated['quantity'];

            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not active. Please contact support.'
                ], 403);
            }
            if (!$product->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is currently unavailable.'
                ], 400);
            }
            if ($product->available_stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$product->available_stock} items available in stock."
                ], 400);
            }

            $unitPrice = $product->price;
            $totalAmount = $unitPrice * $quantity;

            if (!$user->hasSufficientBalance($totalAmount)) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient balance. You need ₦" . number_format($totalAmount - $user->balance) . " more to complete this purchase."
                ], 400);
            }

            DB::beginTransaction();
            try {
                // Create a single order representing multiple logs
                $order = ResellerOrder::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => 'wallet',
                    'payment_status' => 'pending'
                ]);

                // Fetch oldest available logs and assign to this order
                $availableLogs = $product->availableLogs()
                    ->lockForUpdate()
                    ->orderBy('created_at', 'asc')
                    ->limit($quantity)
                    ->get();

                if ($availableLogs->count() < $quantity) {
                    throw new \Exception('Not enough available items in stock.');
                }

                foreach ($availableLogs as $log) {
                    $log->markAsSold($user->id, $order->id);
                }

                // Deduct balance once for the entire order
                $user->deductBalance(
                    $totalAmount,
                    'reseller_purchase',
                    "Reseller purchase: {$product->name} (Qty: {$quantity})",
                    $order
                );

                // Mark the order completed
                $order->markAsCompleted();

                // Update product stock
                $product->updateStock();

                DB::commit();

                // Optional email notification
                // try {
                //     $emailConfig = EmailConfiguration::first();
                //     if ($emailConfig && $emailConfig->email) {
                //         $saleData = $availableLogs->map(function ($log) use ($user, $product, $unitPrice) {
                //             return [
                //                 'order_id' => $log->order_id,
                //                 'category' => 'Reseller Product',
                //                 'name' => $product->name,
                //                 'quantity' => 1,
                //                 'customer_name' => $user->name,
                //                 'price' => $unitPrice,
                //                 'log_id' => $log->id,
                //             ];
                //         })->toArray();

                //         $amount = $totalAmount;
                //         $settings = GeneralSetting::first();
                //         Mail::to($settings->contact_email)->queue(
                //             new SaleNotificationMail('reseller_product', $saleData, $amount, $settings->site_name ?? 'Admin')
                //         );
                //     }
                // } catch (\Exception $e) {
                //     Log::error('Failed to send reseller sales notification email', [
                //         'error' => $e->getMessage(),
                //     ]);
                // }

                return response()->json([
                    'success' => true,
                    'message' => 'Purchase completed successfully!',
                    'data' => [
                        'order' => $order->load('logs'),
                        'total_amount' => $totalAmount,
                        'remaining_balance' => $user->fresh()->balance,
                        'product_name' => $product->name
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Reseller product purchase failed', [
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
            Log::error('Unexpected error in reseller product purchase', [
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
     * Get user's reseller orders.
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

            $query = $user->hasMany(ResellerOrder::class)
                          ->with(['product', 'logs'])
                          ->orderBy('created_at', 'desc');

            if ($status && in_array($status, ['pending', 'completed', 'failed', 'cancelled'])) {
                $query->where('status', $status);
            }
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
            Log::error('Error fetching user reseller orders', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.'
            ], 500);
        }
    }

    /** Show single order with logs for authenticated user */
    public function show(Request $request, ResellerOrder $order)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to view orders.'
                ], 401);
            }
            $user = Auth::user();
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            $order->load(['product', 'logs']);
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing user reseller order', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order.'
            ], 500);
        }
    }
}
