<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\GiftOrder;
use App\Models\Transaction;
use App\Models\EmailConfiguration;
use App\Mail\SaleNotificationMail;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class GiftOrderController extends Controller
{
    use ImageUploadTrait;

    /**
     * Store a newly created gift order in storage.
     */
    public function store(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to place a gift order.'
                ], 401);
            }

            // Validate the request
            $validated = $request->validate([
                'gift_id' => 'required|exists:gifts,id',
                'recipient_name' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:20',
                'sender_name' => 'required|string|max:255',
                'sender_phone' => 'required|string|max:20',
                'sender_email' => 'required|email|max:255',
                'delivery_address' => 'required|string|max:500',
                'delivery_apartment' => 'nullable|string|max:100',
                'delivery_city' => 'required|string|max:100',
                'delivery_state' => 'required|string|max:100',
                'delivery_country' => 'required|string|max:100',
                'delivery_zip' => 'nullable|string|max:20',
                'customize_gift' => 'boolean',
                'custom_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            ]);

            $user = Auth::user();
            $gift = Gift::findOrFail($validated['gift_id']);
            $quantity = 1;

            // Check if user is active
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not active. Please contact support.'
                ], 403);
            }

            // Check if gift is available
            if (!$gift->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'This gift is currently unavailable.'
                ], 400);
            }

            // Calculate total amount
            $unitPrice = $gift->price;
            $customizationCost = 0;
            $isCustomized = $validated['customize_gift'] ?? false;
            
            if ($isCustomized && $gift->customizable) {
                $customizationCost = $gift->customization_cost ?? 0;
            }
            
            $totalAmount = $unitPrice + $customizationCost;

            // Check if user has sufficient balance
            if (!$user->hasSufficientBalance($totalAmount)) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient balance. You need ₦" . number_format($totalAmount - $user->balance) . " more to complete this purchase."
                ], 400);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Handle custom image upload
                $customImagePath = null;
                if ($isCustomized && $request->hasFile('custom_image')) {
                    $customImagePath = $this->uploadImage($request, 'custom_image', 'uploads/gift-customizations');
                }

                // Create gift order
                $giftOrder = GiftOrder::create([
                    'user_id' => $user->id,
                    'gift_id' => $gift->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'customization_cost' => $customizationCost,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => 'wallet',
                    'payment_status' => 'pending',
                    'recipient_name' => $validated['recipient_name'],
                    'recipient_phone' => $validated['recipient_phone'],
                    'sender_name' => $validated['sender_name'],
                    'sender_phone' => $validated['sender_phone'],
                    'sender_email' => $validated['sender_email'],
                    'delivery_address' => $validated['delivery_address'],
                    'delivery_apartment' => $validated['delivery_apartment'],
                    'delivery_city' => $validated['delivery_city'],
                    'delivery_state' => $validated['delivery_state'],
                    'delivery_country' => $validated['delivery_country'],
                    'delivery_zip' => $validated['delivery_zip'],
                    'is_customized' => $isCustomized,
                    'custom_image' => $customImagePath
                ]);

                // Deduct balance from user with transaction logging
                $user->deductBalance(
                    $totalAmount,
                    'gift_purchase',
                    "Gift purchase: {$gift->name} for {$validated['recipient_name']}",
                    $giftOrder
                );

                // Mark order as confirmed (payment successful)
                $giftOrder->markAsPaid();

                DB::commit();

                // Log the successful order
                Log::info('Gift order created successfully', [
                    'user_id' => $user->id,
                    'gift_order_id' => $giftOrder->id,
                    'gift_id' => $gift->id,
                    'total_amount' => $totalAmount,
                    'is_customized' => $validated['is_customized'] ?? false
                ]);

                // Send sales notification email
                try {
                    $emailConfig = EmailConfiguration::first();
                    if ($emailConfig && $emailConfig->email) {
                        $saleData = [
                            'order_id' => $giftOrder->id,
                            'order_number' => $giftOrder->order_number,
                            'gift_name' => $gift->name,
                            'recipient_name' => $giftOrder->recipient_name,
                            'sender_name' => $giftOrder->sender_name,
                            'customer_name' => $user->name,
                            'price' => $giftOrder->total_amount
                        ];

                        Mail::send('mail.sale-notification', $saleData, function ($message) use ($emailConfig) {
                            $message->to($emailConfig->email)
                                ->subject('New Sale Notification');
                        });
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send sales notification email', [
                        'error' => $e->getMessage(),
                        'gift_order_id' => $giftOrder->id
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Gift order placed successfully! Your gift will be delivered soon.',
                    'data' => [
                        'order_id' => $giftOrder->id,
                        'order_number' => $giftOrder->order_number,
                        'gift_name' => $gift->name,
                        'total_amount' => $totalAmount,
                        'remaining_balance' => $user->fresh()->balance,
                        'delivery_date' => $giftOrder->delivery_date,
                        'recipient_name' => $giftOrder->recipient_name
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                // Delete uploaded image if transaction failed
                if ($customImagePath) {
                    $this->deleteImage($customImagePath);
                }
                
                Log::error('Gift order creation failed during transaction', [
                    'user_id' => $user->id,
                    'gift_id' => $gift->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gift order failed: ' . $e->getMessage()
                ], 500);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in gift order creation', [
                'user_id' => Auth::id(),
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
     * Get user's gift orders.
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
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $search = $request->get('search');

            $query = $user->giftOrders()
                          ->with(['gift'])
                          ->orderBy('created_at', 'desc');

            // Filter by status
            if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])) {
                $query->where('status', $status);
            }

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('gift', function ($giftQuery) use ($search) {
                        $giftQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%");
                });
            }

            $orders = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user gift orders', [
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
     * Get specific gift order details.
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
            $order = $user->giftOrders()
                          ->with(['gift'])
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

    /**
     * Cancel a gift order (if allowed).
     */
    public function cancel($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to cancel orders.'
                ], 401);
            }

            $user = Auth::user();
            $order = $user->giftOrders()->findOrFail($id);

            // Check if order can be cancelled
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be cancelled as it is already being processed.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Refund the amount to user's balance with transaction logging
                $user->addBalance(
                    $order->total_amount,
                    'gift_refund',
                    "Gift order cancellation refund: {$order->gift->name}",
                    $order
                );

                // Update order status
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'refunded'
                ]);

                DB::commit();

                Log::info('Gift order cancelled', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'refund_amount' => $order->total_amount
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully. Amount has been refunded to your wallet.',
                    'data' => [
                        'refund_amount' => $order->total_amount,
                        'new_balance' => $user->fresh()->balance
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error cancelling gift order', [
                'user_id' => Auth::id(),
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order. Please try again.'
            ], 500);
        }
    }

    /**
     * Get order status.
     */
    public function checkStatus(GiftOrder $giftOrder)
    {
        // Ensure user can only check their own orders
        if ($giftOrder->user_id != Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $giftOrder->order_number,
                'status' => $giftOrder->status,
                'payment_status' => $giftOrder->payment_status,
                'tracking_number' => $giftOrder->tracking_number,
                'shipped_at' => $giftOrder->shipped_at,
                'delivered_at' => $giftOrder->delivered_at
            ]
        ]);
    }
}