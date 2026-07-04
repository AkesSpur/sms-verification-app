<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use App\Models\User;
use App\Models\Transaction;
use App\Models\BlacklistedNumber;
use App\Models\EmailConfiguration;
use App\Mail\SaleNotificationMail;
use App\Services\SmsBowerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InternationalNumberController extends Controller
{
    protected $smsService;
    protected $maxOrdersPerHour = 20;
    protected $maxOrdersPerDay = 80;
    protected $minBalanceRequired = 100.00; // Minimum balance in Naira

    public function __construct(SmsBowerService $smsService)
    {
        $this->smsService = $smsService;
        $this->middleware('auth');
        
        // Apply throttle middleware only for non-admin users
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            return app('Illuminate\Routing\Middleware\ThrottleRequests')->handle($request, $next, 60, 1);
        })->only(['store', 'checkStatus']);
    }

    /**
     * Check availability for international numbers
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'country' => 'required|integer',
            'service' => 'required|string'
        ]);

        $countryId = (int) $request->input('country');
        $serviceCode = $request->input('service');
        $user = Auth::user();

        try {
            // Get live price and stock from SmsBower API
            $priceData = $this->smsService->getPrice($countryId, $serviceCode);

            if (!$priceData || $priceData['count'] === 0) {
                return response()->json([
                    'success' => true,
                    'available' => false,
                    'message' => 'Service temporarily unavailable. Please try again later.'
                ]);
            }

            $priceInNaira = $this->smsService->calculateNairaPrice($priceData['cost']);

            // Check user balance
            if ($user->balance < $priceInNaira) {
                return response()->json([
                    'success' => false,
                    'available' => false,
                    'message' => 'Insufficient balance. Required: ₦' . number_format($priceInNaira, 2)
                ]);
            }

            return response()->json([
                'success' => true,
                'available' => true,
                'message' => 'Service is available!',
                'price' => $priceInNaira,
                'formatted_price' => '₦' . number_format($priceInNaira, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('International number availability check failed', [
                'user_id' => $user->id,
                'country' => $countryId,
                'service' => $serviceCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Unable to check availability. Please try again later.'
            ], 500);
        }
    }

    /**
     * Purchase international number
     */
    public function store(Request $request)
    {
        $request->validate([
            'country' => 'required|integer',
            'service' => 'required|string',
            'country_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255'
        ]);

        $countryId = (int) $request->input('country');
        $serviceCode = $request->input('service');
        $countryName = $request->input('country_name');
        $serviceName = $request->input('service_name');
        $user = Auth::user();

        // Rate limiting (skip for admin users)
        if ($user->role !== 'admin') {
            $purchaseKey = "international_purchase:{$user->id}";
            if (RateLimiter::tooManyAttempts($purchaseKey, $this->maxOrdersPerHour)) {
                $seconds = RateLimiter::availableIn($purchaseKey);
                throw ValidationException::withMessages([
                    'service' => "Too many purchase attempts. Please try again in {$seconds} seconds."
                ]);
            }
        }

        try {
            return DB::transaction(function () use ($countryId, $serviceCode, $countryName, $serviceName, $user) {
                // Live price and stock check from SmsBower API
                $priceData = $this->smsService->getPrice($countryId, $serviceCode);

                if (!$priceData || $priceData['count'] === 0) {
                    throw ValidationException::withMessages([
                        'service' => 'Service is no longer available for this country.'
                    ]);
                }

                $priceInNaira = $this->smsService->calculateNairaPrice($priceData['cost']);

                if ($user->balance < $priceInNaira) {
                    throw ValidationException::withMessages([
                        'balance' => 'Insufficient balance. Required: ₦' . number_format($priceInNaira, 2)
                    ]);
                }

                // Check for active orders
                $activeOrders = Order::where('user_id', $user->id)
                    ->where('status', Order::STATUS_PENDING)
                    ->count();

                if ($activeOrders >= 3) {
                    throw ValidationException::withMessages([
                        'service' => 'You have too many active orders. Please complete or cancel existing orders first.'
                    ]);
                }

                // Resolve DB rows for the order foreign keys (match by name, create if missing)
                $countryModel = Country::whereRaw('LOWER(name) = ?', [strtolower($countryName)])->first()
                    ?? Country::create([
                        'name' => $countryName,
                        'code' => 'SB-' . $countryId,
                        'flag' => '🌍'
                    ]);

                $service = Service::firstOrCreate(
                    ['code' => $serviceCode],
                    ['name' => $serviceName, 'status' => 'active', 'price' => 0]
                );

                // Request number from SmsBower API
                $response = $this->smsService->purchaseNumber($countryId, $serviceCode);

                if (isset($response['error'])) {
                    throw new \Exception($response['error']);
                }

                $phoneNumber = $response['number'];
                $activationId = $response['order_id'];

                // Create order (20-minute SMS window)
                $order = Order::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'country_id' => $countryModel->id,
                    'phone_number' => (string) $phoneNumber,
                    'activation_id' => $activationId,
                    'price' => $priceInNaira,
                    'api_price' => $priceData['cost'],
                    'final_price' => $priceInNaira,
                    'status' => Order::STATUS_PENDING,
                    'expires_at' => Carbon::now()->addMinutes(20),
                    'sms_window_expires_at' => Carbon::now()->addMinutes(20),
                    'order_source' => 'international_numbers_web',
                    'api_provider' => 'smsbower',
                    'api_response' => json_encode($response)
                ]);

                $user->deductBalance(
                    $priceInNaira,
                    'sms_purchase',
                    "SMS number purchase for {$service->name} ({$countryModel->name})",
                    $order
                );

                // Hit rate limiter (skip for admin users)
                if ($user->role !== 'admin') {
                    $purchaseKey = "international_purchase:{$user->id}";
                    RateLimiter::hit($purchaseKey, 3600);
                }

                Log::info('International number purchased successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'country' => $countryId,
                    'service' => $serviceCode,
                    'phone_number' => $phoneNumber
                ]);

                // Send sales notification email
                $this->sendSalesNotificationEmail($order);

                return response()->json([
                    'success' => true,
                    'message' => 'International number purchased successfully!',
                    'order' => [
                        'id' => $order->id,
                        'phone_number' => $phoneNumber,
                        'service' => $service->name,
                        'country' => $countryModel->name,
                        'expires_at' => $order->expires_at->format('Y-m-d H:i:s'),
                        'status' => $order->status
                    ]
                ]);
            });

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('International number purchase failed', [
                'user_id' => $user->id,
                'country' => $countryId,
                'service' => $serviceCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to purchase number. Please try again later.'
            ], 500);
        }
    }

    /**
     * Check order status
     */
    public function checkStatus($orderId)
    {
        $user = Auth::user();
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Rate limiting for status checks (skip for admin users)
        if ($user->role !== 'admin') {
            $statusKey = "status_check:{$user->id}:{$orderId}";
            if (RateLimiter::tooManyAttempts($statusKey, 30)) {
                return response()->json([
                    'error' => 'Too many status checks. Please wait before checking again.'
                ], 429);
            }
            RateLimiter::hit($statusKey, 60);
        }

        try {
            // Check if order should be auto-cancelled (1 minute past SMS window expiration)
            if ($order->sms_window_expires_at && 
                $order->sms_window_expires_at->addMinute()->isPast() && 
                !$order->sms_received_at && 
                in_array($order->status, ['pending', 'active'])) {
                
                // Auto-cancel the order without API request since it's expired
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Auto-cancelled: SMS window expired (1+ minute past expiration)'
                ]);
                
                // Process refund
                $order->processRefund('Auto-cancelled: SMS window expired', 'system');
                
                return response()->json([
                    'success' => true,
                    'order' => [
                        'status' => 'cancelled',
                        'sms_code' => null,
                        'phone_number' => $order->phone_number,
                        'expires_at' => null
                    ],
                    'message' => 'Order has been automatically cancelled due to SMS timeout. Your account has been refunded.'
                ]);
            }
            
            return DB::transaction(function () use ($order, $user, $orderId) {

                // Check if order is already completed or expired
                if (in_array($order->status, ['completed', 'expired', 'cancelled'])) {
                    return response()->json([
                        'success' => true,
                        'order' => [
                            'status' => $order->status,
                            'sms_code' => $order->sms_code,
                            'phone_number' => $order->phone_number,
                            'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                        ],
                        'message' => 'Order status: ' . ucfirst($order->status)
                    ]);
                }

                // Get status from SmsBower API
                $status = $this->smsService->checkSms($order->activation_id);

                if (isset($status['status'])) {
                    $apiStatus = $status['status'];

                    if ($apiStatus === 'completed' && !empty($status['code'])) {
                        // Extract verification code from the message using regex
                        $extractedCode = $this->extractVerificationCode($status['code']);
                        $codeToStore = $extractedCode ?: $status['code'];

                        $order->update([
                            'sms_code' => $codeToStore,
                            'sms_received_at' => now(),
                            'status' => Order::STATUS_COMPLETED
                        ]);

                        Log::info('International SMS code received', [
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'full_message' => $status['code'],
                            'extracted_code' => $extractedCode
                        ]);
                        
                        // Send sales notification email
                        $this->sendSalesNotificationEmail($order);
                        
                        return response()->json([
                            'success' => true,
                            'order' => [
                                'status' => Order::STATUS_COMPLETED,
                                'sms_code' => $codeToStore,
                                'phone_number' => $order->phone_number,
                                'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                            ],
                            'message' => 'SMS code received successfully!'
                        ]);
                    }
                    
                    if (in_array($apiStatus, ['cancelled', 'expired'])) {
                        $order->update(['status' => Order::STATUS_EXPIRED]);

                        // Refund if the provider cancelled before any SMS arrived
                        if (is_null($order->sms_code)) {
                            $order->processRefund('Cancelled by provider', 'system');
                        }

                        return response()->json([
                            'success' => true,
                            'order' => [
                                'status' => Order::STATUS_EXPIRED,
                                'sms_code' => $order->sms_code,
                                'phone_number' => $order->phone_number,
                                'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                            ],
                            'message' => 'Order was cancelled or expired by provider.'
                        ]);
                    }
                }

                // No changes
                return response()->json([
                    'success' => true,
                    'order' => [
                        'status' => $order->status,
                        'sms_code' => $order->sms_code,
                        'phone_number' => $order->phone_number,
                        'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                    ],
                    'message' => 'No SMS received yet. Keep waiting...'
                ]);
            });

        } catch (\Exception $e) {
            Log::error('International order status check failed', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to check order status. Please try again later.'
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();
        
        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found or cannot be cancelled'], 404);
        }

        if (!$order) {
            return response()->json(['error' => 'Order not found or cannot be cancelled'], 404);
        }

        // Check if order can be cancelled (within 20 minutes)
        if ($order->created_at->diffInMinutes(now()) > 20) {
            return response()->json([
                'error' => 'Order cannot be cancelled after 20 minutes'
            ], 400);
        }

        // Check if order is in cancellable status
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ACTIVE])) {
            return response()->json([
                'error' => 'Order cannot be cancelled in current status'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($order) {
                $order->cancel('Cancelled by user');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully. Refund has been processed.'
                ]);
            });
        } catch (\Exception $e) {
            Log::error('International order cancellation failed', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to cancel order. Please try again later.'
            ], 500);
        }
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            abort(404);
        }

        // If this is an AJAX request, return JSON
        if (request()->ajax()) {
            $timeRemaining = null;
            if ($order->sms_window_expires_at && $order->sms_window_expires_at->isFuture()) {
                $timeRemaining = $order->sms_window_expires_at->diffInSeconds(now());
            }

            return response()->json([
                'id' => $order->id,
                'phone_number' => $order->phone_number,
                'service' => $order->service->name ?? 'Unknown',
                'status' => $order->status,
                'sms_code' => $order->sms_code,
                'created_at' => $order->created_at->format('M d, Y H:i'),
                'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->format('M d, Y H:i') : null,
                'refunded' => $order->refunded,
                'time_remaining' => $timeRemaining
            ]);
        }

        // Return the order details view
        return view('user.international-order-details', compact('order'));
    }

    /**
     * Get country name from code
     */
    private function getCountryName($countryCode)
    {
        $countryNames = [
            'uk' => 'United Kingdom',
            'ca' => 'Canada',
            'au' => 'Australia',
            'de' => 'Germany',
            'fr' => 'France',
            'it' => 'Italy',
            'es' => 'Spain',
            'nl' => 'Netherlands',
            'se' => 'Sweden',
            'no' => 'Norway',
        ];

        return $countryNames[$countryCode] ?? 'Unknown';
    }

    /**
     * Get country flag from code
     */
    private function getCountryFlag($countryCode)
    {
        $flags = [
            'uk' => '🇬🇧',
            'ca' => '🇨🇦',
            'au' => '🇦🇺',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'it' => '🇮🇹',
            'es' => '🇪🇸',
            'nl' => '🇳🇱',
            'se' => '🇸🇪',
            'no' => '🇳🇴',
        ];

        return $flags[$countryCode] ?? '🌍';
    }

    /**
     * Process API response
     */
    private function processApiResponse(Order $order, string $response)
    {
        if ($response === 'STATUS_CANCEL') {
            $order->update(['status' => Order::STATUS_EXPIRED]);
            return response()->json([
                'success' => true,
                'order' => [
                    'status' => Order::STATUS_EXPIRED,
                    'sms_code' => $order->sms_code,
                    'phone_number' => $order->phone_number,
                    'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                ],
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if (strpos($response, 'STATUS_OK') !== false) {
            $fullMessage = explode(':', $response)[1] ?? null;
            if ($fullMessage) {
                // Extract verification code from the full message
                $extractedCode = $this->extractVerificationCode($fullMessage);
                $codeToStore = $extractedCode ?: $fullMessage; // Use extracted code or full message as fallback
                
                $order->update([
                    'sms_code' => $codeToStore,
                    'sms_received_at' => Carbon::now(),
                    'status' => Order::STATUS_COMPLETED
                ]);

                Log::info('International SMS code received', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'full_message' => $fullMessage,
                    'extracted_code' => $extractedCode
                ]);

                return response()->json([
                    'success' => true,
                    'order' => [
                        'status' => Order::STATUS_COMPLETED,
                        'sms_code' => $codeToStore,
                        'phone_number' => $order->phone_number,
                        'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                    ],
                    'message' => 'SMS code received successfully!'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'order' => [
                'status' => $order->status,
                'sms_code' => $order->sms_code,
                'phone_number' => $order->phone_number,
                'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
            ],
            'message' => 'Waiting for SMS...'
        ]);
    }

    /**
     * Send sales notification email for SMS orders
     */
    private function sendSalesNotificationEmail(Order $order)
    {
        try {
            $emailConfig = EmailConfiguration::first();
            if ($emailConfig && $emailConfig->email) {
                $saleData = [
                    'order_id' => $order->id,
                    'phone_number' => $order->phone_number,
                    'service_name' => $order->service->name ?? 'N/A',
                    'country' => $order->country->name ?? 'N/A',
                    'customer_name' => $order->user->name,
                    'price' => $order->final_price
                ];

            $settings = GeneralSetting::first();

            Mail::to($settings->contact_email)->queue(
                    new SaleNotificationMail('sms', $saleData, $saleData['price'], $settings->site_name ?? 'Admin')
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send sales notification email', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
        }
    }

    /**
     * Process API status
     */
    private function processApiStatus(Order $order, string $status, ?string $code = null)
    {
        if ($status === 'STATUS_CANCEL') {
            $order->update(['status' => Order::STATUS_EXPIRED]);
            return response()->json([
                'success' => true,
                'order' => [
                    'status' => Order::STATUS_EXPIRED,
                    'sms_code' => $order->sms_code,
                    'phone_number' => $order->phone_number,
                    'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                ],
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if ($status === 'STATUS_OK' && $code) {
            $order->update([
                'sms_code' => $code,
                'sms_received_at' => Carbon::now(),
                'status' => Order::STATUS_COMPLETED
            ]);

            Log::info('International SMS code received', [
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);

            // Send sales notification email
            $this->sendSalesNotificationEmail($order);

            return response()->json([
                'success' => true,
                'order' => [
                    'status' => Order::STATUS_COMPLETED,
                    'sms_code' => $code,
                    'phone_number' => $order->phone_number,
                    'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                ],
                'message' => 'SMS code received successfully!'
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'status' => $order->status,
                'sms_code' => $order->sms_code,
                'phone_number' => $order->phone_number,
                'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
            ],
            'message' => 'Waiting for SMS...'
        ]);
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $orderId = null)
    {
        $orderId = $orderId ?? $request->route('order');
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACTIVE])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or cannot be cancelled'], 404);
        }

        // Check if order has already been refunded to prevent multiple refunds
        if ($order->refunded) {
            return response()->json([
                'error' => 'Order has already been refunded and cannot be cancelled again'
            ], 400);
        }

        // Check if order can be cancelled (within 20 minutes)
        if ($order->created_at->diffInMinutes(now()) > 20) {
            return response()->json([
                'error' => 'Order cannot be cancelled after 20 minutes'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($order, $user) {
                // Cancel with the SmsBower provider — only refund when the provider confirms,
                // otherwise the number stays active (and billable) on their side
                if ($order->activation_id) {
                    $cancelResult = $this->smsService->cancelOrder($order->activation_id);

                    if (!$cancelResult['success']) {
                        return response()->json([
                            'success' => false,
                            'early_cancel' => $cancelResult['early_cancel'] ?? false,
                            'message' => $cancelResult['message']
                        ], 422);
                    }
                }

                // Double-check refund status within transaction to prevent race conditions
                if ($order->refunded) {
                    return response()->json([
                        'error' => 'Order has already been refunded'
                    ], 400);
                }

                // Update order status
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'cancelled_at' => Carbon::now()
                ]);

                // Refund if no SMS was received
                if (is_null($order->sms_code)) {
                    // Use the price that was actually charged (stored in order)
                    $refundAmount = $order->final_price ?? $order->amount;

                    $user->increment('balance', $refundAmount);
                    $order->update(['refunded' => true]);

                    // Create refund transaction
                    Transaction::createTransaction(
                        $user,
                        'credit',
                        'sms_refund',
                        $refundAmount,
                        "Refund for cancelled International SMS order #{$order->id}",
                        [
                            'original_order_id' => $order->id,
                            'reason' => 'user_cancelled'
                        ],
                        $order
                    );

                    Log::info('International order cancelled with refund', [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'refund_amount' => $refundAmount
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order cancelled and refunded successfully.',
                        'refund_amount' => $refundAmount,
                        'order' => [
                            'id' => $order->id,
                            'status' => Order::STATUS_CANCELLED
                        ]
                    ]);
                } else {
                    Log::info('International order cancelled without refund', [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'reason' => 'sms_already_received'
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order cancelled. No refund as SMS was already received.',
                        'order' => [
                            'id' => $order->id,
                            'status' => Order::STATUS_CANCELLED
                        ]
                    ]);
                }
            });

        } catch (\Exception $e) {
            Log::error('International order cancellation failed', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to cancel order. Please try again later.'
            ], 500);
        }
    }

    /**
     * Extract verification code from SMS message using various patterns
     */
    private function extractVerificationCode($message)
    {
        if (!$message) {
            return null;
        }

        // Common patterns for verification codes (4-8 digits)
        $patterns = [
            '/\b(\d{4,8})\b/',                    // Any 4-8 digit number
            '/code[:\s]*(\d{4,8})/i',             // "code: 123456" or "code 123456"
            '/verification[:\s]*(\d{4,8})/i',     // "verification: 123456"
            '/pin[:\s]*(\d{4,8})/i',              // "pin: 1234"
            '/otp[:\s]*(\d{4,8})/i',              // "otp: 123456"
            '/confirm[:\s]*(\d{4,8})/i',          // "confirm: 123456"
            '/security[:\s]*(\d{4,8})/i',         // "security: 123456"
            '/access[:\s]*(\d{4,8})/i',           // "access: 123456"
            '/login[:\s]*(\d{4,8})/i',            // "login: 123456"
            '/\b(\d{4,8})\s*is\s*your/i',        // "123456 is your code"
            '/your.*?(\d{4,8})/i',                // "your code is 123456"
            '/use[:\s]*(\d{4,8})/i',              // "use: 123456"
            '/enter[:\s]*(\d{4,8})/i',            // "enter: 123456"
            '/amazon[^\d]*?(\d{4,8})/i',           // Amazon specific pattern - handles 'Amazon: Your code is 123456'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}