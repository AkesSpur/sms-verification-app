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
use App\Services\SmsPoolService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UsaNumberController extends Controller
{
    protected $smsService;
    protected $pricingService;
    protected $usaCountryCode; // USA country code for SMS Activate
    protected $maxOrdersPerHour = 20;
    protected $maxOrdersPerDay = 80;
    protected $minBalanceRequired = 100.00; // Minimum balance in Naira

    public function __construct(SmsPoolService $smsService, PricingService $pricingService)
    {
        $this->smsService = $smsService;
        $this->pricingService = $pricingService;
        $this->usaCountryCode = SmsPoolService::getUsaCountryCode();
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
     * Display USA numbers page
     */
    public function index()
    {
        $user = auth()->user();
        $usaCountryId = $this->getUsaCountryId();
        
        // Get user's active orders for USA numbers
        $activeOrders = Order::where('user_id', $user->id)
            ->where('country_id', $usaCountryId)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACTIVE])
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all user's USA orders for history display with pagination
        $allOrders = Order::where('user_id', $user->id)
            ->where('country_id', $usaCountryId)
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get available services for USA using proper relationship
        $services = Service::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Get user statistics
        $stats = [
            'balance' => $user->balance ?? 0,
            'total_orders' => Order::where('user_id', $user->id)->where('country_id', $usaCountryId)->count(),
            'active_orders' => $activeOrders->count(),
            'completed_orders' => Order::where('user_id', $user->id)
                ->where('country_id', $usaCountryId)
                ->where('status', Order::STATUS_COMPLETED)
                ->count(),
        ];
        
        return view('user.usa-numbers', compact('activeOrders', 'allOrders', 'services', 'stats'));
    }

    /**
     * Check service availability for USA
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'service' => 'required|string|exists:services,code'
        ]);

        $user = Auth::user();
        $serviceCode = $request->service;

        // Rate limiting for availability checks (skip for admin users)
        if ($user->role !== 'admin') {
            $key = 'availability_check:' . $user->id;
            if (RateLimiter::tooManyAttempts($key, 10)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Too many requests. Please wait before checking again.'
                ], 429);
            }
            RateLimiter::hit($key, 60);
        }

        try {
            // Check if service exists and is active
            $service = Service::where('code', $serviceCode)
                ->where('status', 'active')
                // ->whereHas('countries', function($query) {
                //     $query->where('countries.code', $this->usaCountryCode)
                //           ->where('country_service.is_active', true);
                // })
                ->first();

            if (!$service) {
                return response()->json([
                    'available' => false,
                    'message' => 'Service not available for USA numbers.'
                ]);
            }

            // Check user's recent order history for this service
            $recentOrders = Order::where('user_id', $user->id)
                ->where('service_id', $service->id)
                ->where('created_at', '>', now()->subHour())
                ->count();

            if ($recentOrders >= 20) {
                return response()->json([
                    'available' => false,
                    'message' => 'You have reached the hourly limit for this service.'
                ]);
            }

            // Get price for USA in Naira
            $price = $this->pricingService->getServicePrice($service->id, $this->getUsaCountryId());
            // $balance = $this->smsService->getBalance();
            // Check API availability (cached for 5 minutes)
            $cacheKey = "usa_availability_{$serviceCode}";
            $balance = $this->smsService->getBalance();
            Log::info('Account Balance', ['balance '=> $balance]);
            $availability = Cache::remember($cacheKey, 300, function() use ($service) {
                try {
                    Log::info('🔍 Checking SMSPool API availability', [
                        'service_code' => $service->code,
                        'service_id' => $service->code,
                        'country_code' => $this->usaCountryCode
                    ]);
                    
                    $result = $this->smsService->checkAvailability($service->code, $this->smsService->getUsaCountry()['code']);
                    
                    Log::info('📡 SMSPool API Response', [
                        'service_code' => $service->code,
                        'raw_result' => $result,
                        'available_status' => is_array($result) ? ($result['available'] ?? 'not_set') : (is_bool($result) ? ($result ? 'true' : 'false') : 'unknown')
                    ]);
                    
                    // Handle both array and boolean responses
                    if (is_array($result)) {
                        return $result['available'] ?? false;
                    } elseif (is_bool($result)) {
                        return $result;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    Log::warning('❌ Failed to check USA availability', [
                        'service' => $service->code,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return false;
                }
            });

            Log::info('✅ Final availability check result', [
                'service_code' => $serviceCode,
                'availability' => $availability,
                'price' => $price,
                'user_id' => $user->id
            ]);

            return response()->json([
                'available' => $availability,
                'price' => $price,
                'message' => $availability ? 'USA numbers available for this service.' : 'No USA numbers currently available for this service.'
            ]);

        } catch (\Exception $e) {
            Log::error('USA availability check failed', [
                'user_id' => $user->id,
                'service' => $serviceCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'available' => false,
                'message' => 'Unable to check availability. Please try again later.'
            ], 500);
        }
    }

    /**
     * Purchase USA number
     */
    public function store(Request $request)
    {
        $request->validate([
            'service' => 'required|string|exists:services,code'
        ]);

        $user = Auth::user();
        $serviceCode = $request->service;

        // Rate limiting for purchases (skip for admin users)
        $purchaseKey = 'usa_purchase:'. $user->id;
        if ($user->role !== 'admin') {
            if (RateLimiter::tooManyAttempts($purchaseKey, $this->maxOrdersPerHour)) {
                throw ValidationException::withMessages([
                    'service' => 'You have reached the hourly purchase limit.'
                ]);
            }

            // Daily limit check
            $dailyOrders = Order::where('user_id', $user->id)
                ->where('created_at', '>', now()->subDay())
                ->count();

            if ($dailyOrders >= $this->maxOrdersPerDay) {
                throw ValidationException::withMessages([
                    'service' => 'You have reached the daily purchase limit.'
                ]);
            }
        }

        // Security checks
        $this->performSecurityChecks($user);

        try {
            return DB::transaction(function () use ($user, $serviceCode, $purchaseKey) {
                // Get service
                $service = Service::where('code', $serviceCode)
                    ->where('status', 'active')
                    // ->whereHas('countries', function($query) {
                    //     $query->where('countries.code', $this->usaCountryCode)
                    //           ->where('country_service.is_active', true);
                    // })
                    ->lockForUpdate()
                    ->first();

                if (!$service) {
                    throw ValidationException::withMessages([
                        'service' => 'Service not available for USA numbers.'
                    ]);
                }

                // Get price in Naira from PricingService (already rounded to next 10th)
                $priceInNaira = $this->pricingService->getServicePrice($service->id, $this->getUsaCountryId());
                
                if ($user->balance < $priceInNaira) {
                    throw ValidationException::withMessages([
                        'balance' => 'Insufficient balance. Required: ₦' . number_format($priceInNaira, 2)
                    ]);
                }

                // Check for active orders
                $activeOrders = Order::where('user_id', $user->id)
                    ->where('status', Order::STATUS_PENDING)
                    ->count();

                if ($activeOrders >= 8) {
                    throw ValidationException::withMessages([
                        'service' => 'You have too many active orders. Please complete or cancel existing orders first.'
                    ]);
                }

                // Request number from API with Naira price (service will handle USD conversion internally)
                $response = $this->smsService->purchaseNumber($service->code, $user->id, $this->smsService->getUsaCountry()['code'], $priceInNaira, 'usa_numbers_web');

                if (!$response['success']) {
                    throw new \Exception('Failed to get USA number from provider.');
                }

                $order = $response['order'];
                $phoneNumber = $response['phone_number'];
                $activationId = $response['activation_id'];
                $country = $response['country'];

                // Transaction and balance deduction are handled by SmsPoolService

                // Hit rate limiter (skip for admin users)
                if ($user->role !== 'admin') {
                    RateLimiter::hit($purchaseKey, 3600);
                }

                Log::info('USA number purchased successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'service' => $serviceCode,
                    'phone_number' => $phoneNumber
                ]);

                // Send sales notification email for testing
                $this->sendSalesNotificationEmail($order);

                return response()->json([
                    'success' => true,
                    'message' => 'USA number purchased successfully!',
                    'order' => [
                        'id' => $order->id,
                        'phone_number' => $phoneNumber,
                        'service' => $service->name,
                        'expires_at' => $order->expires_at->format('Y-m-d H:i:s'),
                        'status' => $order->status
                    ]
                ]);
            });

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('USA number purchase failed', [
                'user_id' => $user->id,
                'service' => $serviceCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase USA number. Please try again later.'
            ], 500);
        }
    }

    /**
     * Check SMS status for order
     */
    public function checkStatus($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            // ->whereHas('service.countries', function($query) {
            //     $query->where('code', $this->usaCountryCode);
            // })
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

            // Check if order has expired
            if ($order->isExpired()) {
                return $this->handleExpiredOrder($order);
            }

            // Get status from API
            $status = $this->smsService->checkSmsStatus($order->activation_id);

            return DB::transaction(function () use ($order, $status) {
                // Handle SMSPool API response format
                if (isset($status['status'])) {
                    $apiStatus = $status['status'];
                    
                    if ($apiStatus === 'completed' && isset($status['code'])) {
                        // Extract verification code from the message using regex
                        $extractedCode = $this->extractVerificationCode($status['code']);
                        
                        $order->update([
                            'sms_code' => $extractedCode ?: $status['code'],
                            'sms_received_at' => now(),
                            'status' => Order::STATUS_COMPLETED
                        ]);

                        Log::info('USA SMS code received', [
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'full_message' => $status['full_sms'] ?? $status['code'],
                            'extracted_code' => $extractedCode
                        ]);

                        return response()->json([
                            'success' => true,
                            'order' => [
                                'status' => Order::STATUS_COMPLETED,
                                'sms_code' => $extractedCode ?: $status['code'],
                                'phone_number' => $order->phone_number
                            ],
                            'message' => 'SMS code received successfully!'
                        ]);
                    }
                    
                    if (in_array($apiStatus, ['cancelled', 'expired'])) {
                        $order->update(['status' => Order::STATUS_EXPIRED]);
                        return response()->json([
                            'success' => true,
                            'order' => [
                                'status' => Order::STATUS_EXPIRED,
                                'sms_code' => $order->sms_code,
                                'phone_number' => $order->phone_number
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
            Log::error('USA order status check failed', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check status. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend SMS code for USA number order
     */
    public function resendSms($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            // ->where('status', 'pending')
            ->where('country_id', $this->getUsaCountryId())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or not eligible for resend'
            ], 404);
        }

        // Check if order is within the 20-minute SMS window
        if (!$order->sms_window_expires_at || $order->sms_window_expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'SMS window has expired. Cannot request resend.'
            ], 400);
        }

        // Allow resending even if SMS code was received, as long as we're within the SMS window
        // This helps users who might have missed the first SMS or need a fresh code

        // Rate limiting for resend requests (max 2 resends per order)
        $resendCount = Cache::get("resend_count_{$order->id}", 0);
        if ($resendCount >= 2) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum resend attempts reached for this order'
            ], 429);
        }

        try {
            // Request SMS retry from SMSPool API
            $result = $this->smsService->requestSmsRetry($order->activation_id);
            
            if ($result['success']) {
                // Increment resend counter
                Cache::put("resend_count_{$order->id}", $resendCount + 1, now()->addHours(1));
                
                Log::info('SMS resend requested', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'activation_id' => $order->activation_id,
                    'resend_count' => $resendCount + 1
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'SMS resend requested successfully. Please wait for the new code.',
                    'resend_count' => $resendCount + 1,
                    'max_resends' => 2
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to request SMS resend. Please try again later.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('SMS resend request failed', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to request SMS resend. Please try again later.'
            ], 500);
        }
    }

    /**
     * Cancel USA number order
     */
    public function cancel($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            // ->whereHas('service.countries', function($query) {
            //     $query->where('countries.code', $this->usaCountryCode);
            // })
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

        // Check if order can be cancelled (within SMS window + 1 minute grace period)
        if ($order->sms_window_expires_at && $order->sms_window_expires_at->addMinute()->isPast()) {
            return response()->json([
                'error' => 'Order cannot be cancelled as SMS window has expired'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($order, $user) {
                // Check if order is past expiration (1+ minute) - skip API call
                $skipApiCall = $order->sms_window_expires_at && 
                              $order->sms_window_expires_at->addMinute()->isPast();
                
                if (!$skipApiCall) {
                    // Cancel with SMSPool API only if within valid timeframe
                    $this->smsService->cancelNumber($order->activation_id);
                }

                // Double-check refund status within transaction to prevent race conditions
                if ($order->refunded) {
                    return response()->json([
                        'error' => 'Order has already been refunded'
                    ], 400);
                }

                // Update order status
                $order->update(['status' => 'cancelled']);

                // Refund if no SMS was received
                if (is_null($order->sms_code)) {
                    // Use the price that was actually charged (stored in order)
                    $refundAmount = $order->final_price;

                    $user->increment('balance', $refundAmount);
                    $order->update(['refunded' => true]);

                    // Create refund transaction
                    Transaction::createTransaction(
                        $user,
                        'credit',
                        'sms_refund',
                        $refundAmount,
                        "Refund for cancelled USA SMS order #{$order->id}",
                        [
                            'original_order_id' => $order->id,
                            'reason' => 'user_cancelled'
                        ],
                        $order
                    );

                    Log::info('USA order cancelled with refund', [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'refund_amount' => $refundAmount
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order cancelled and refunded successfully.',
                        'refund_amount' => $refundAmount
                    ]);
                } else {
                    Log::info('USA order cancelled without refund', [
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'reason' => 'sms_already_received'
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order cancelled. No refund as SMS was already received.'
                    ]);
                }
            });

        } catch (\Exception $e) {
            Log::error('USA order cancellation failed', [
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
     * Get order details
     */
    public function show($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            // ->whereHas('service.countries', function($query) {
            //     $query->where('code', $this->usaCountryCode);
            // })
            ->with(['service', 'user'])
            ->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        // If it's an AJAX request, return JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'order' => [
                    'id' => $order->id,
                    'phone_number' => $order->phone_number,
                    'service' => $order->service->name,
                    'status' => $order->status,
                    'sms_code' => $order->sms_code,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $order->expires_at->format('Y-m-d H:i:s'),
                    'refunded' => $order->refunded,
                    'time_remaining' => $order->expires_at->gt(now()) ? $order->expires_at->diffInMinutes(now()) : 0
                ]
            ]);
        }

        // Return the order details view
        return view('user.usa-order-details', compact('order'));
    }

    /**
     * Security checks before purchase
     */
    private function performSecurityChecks(User $user)
    {
        // Check if user account is suspicious
        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'account' => 'Your account is suspended. Please contact support.'
            ]);
        }

        // Check for suspicious activity
        $recentFailures = Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_EXPIRED)
            ->where('created_at', '>', now()->subHours(24))
            ->count();

        if ($recentFailures >= env('SMS_FRAUD_THRESHOLD', 5)) {
            throw ValidationException::withMessages([
                'security' => 'Too many failed orders. Please contact support.'
            ]);
        }

        // Check minimum balance requirement
        if ($user->balance < $this->minBalanceRequired) {
            throw ValidationException::withMessages([
                'balance' => 'Minimum balance of ₦' . number_format($this->minBalanceRequired, 2) . ' required.'
            ]);
        }
    }

    /**
     * Handle expired order
     */
    private function handleExpiredOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $order->handleExpiration();
            
            return response()->json([
                'success' => true,
                'order' => [
                    'status' => Order::STATUS_EXPIRED,
                    'sms_code' => $order->sms_code,
                    'phone_number' => $order->phone_number
                ],
                'message' => 'Order has expired.',
                'refunded' => $order->refunded
            ]);
        });
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
                    'phone_number' => $order->phone_number
                ],
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if (strpos($response, 'STATUS_OK') !== false) {
            $fullMessage = explode(':', $response, 2)[1] ?? null;
            if ($fullMessage) {
                // Extract verification code from the message using regex
                $extractedCode = $this->extractVerificationCode($fullMessage);
                
                $order->update([
                    'sms_code' => $extractedCode ?: $fullMessage, // Use extracted code or full message as fallback
                    'sms_received_at' => now(),
                    'status' => Order::STATUS_COMPLETED
                ]);

                Log::info('USA SMS code received', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'full_message' => $fullMessage,
                    'extracted_code' => $extractedCode
                ]);

                // Send sales notification email
                $this->sendSalesNotificationEmail($order);

                return response()->json([
                    'success' => true,
                    'order' => [
                        'status' => Order::STATUS_COMPLETED,
                        'sms_code' => $extractedCode ?: $fullMessage,
                        'phone_number' => $order->phone_number
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
                'phone_number' => $order->phone_number
            ],
            'message' => 'Waiting for SMS...'
        ]);
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
                    'phone_number' => $order->phone_number
                ],
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if ($status === 'STATUS_OK' && $code) {
            // Extract verification code from the message using regex
            $extractedCode = $this->extractVerificationCode($code);
            
            $order->update([
                'sms_code' => $extractedCode ?: $code, // Use extracted code or full message as fallback
                'sms_received_at' => now(),
                'status' => Order::STATUS_COMPLETED
            ]);

            Log::info('USA SMS code received', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'full_message' => $code,
                'extracted_code' => $extractedCode
            ]);

            return response()->json([
                'success' => true,
                'order' => [
                    'status' => Order::STATUS_COMPLETED,
                    'sms_code' => $extractedCode ?: $code,
                    'phone_number' => $order->phone_number
                ],
                'message' => 'SMS code received successfully!'
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'status' => $order->status,
                'sms_code' => $order->sms_code,
                'phone_number' => $order->phone_number
            ],
            'message' => 'Waiting for SMS...'
        ]);
    }

    /**
     * Get USA country ID from database
     */
    private function getUsaCountryId()
    {
        return Country::where('code', $this->usaCountryCode)->value('id') ?? 1;
    }

    /**
     * Extract verification code from SMS message using regex patterns
     */
    private function extractVerificationCode($message)
    {
        // Common patterns for verification codes
        $patterns = [
            // 6-digit codes (most common)
            '/\b(\d{6})\b/',
            // 4-digit codes
            '/\b(\d{4})\b/',
            // 5-digit codes
            '/\b(\d{5})\b/',
            // Codes with "code is" or "code:" prefix
            '/(?:code\s*(?:is|:)\s*)(\d{4,8})/i',
            // Codes with "verification code" prefix
            '/(?:verification\s*code\s*(?:is|:)?\s*)(\d{4,8})/i',
            // Codes with "your code" prefix
            '/(?:your\s*code\s*(?:is|:)?\s*)(\d{4,8})/i',
            // Amazon specific pattern - handles 'Amazon: Your code is 123456'
            '/amazon[^\d]*?(\d{4,8})/i',
            // Generic: any sequence of 4-8 digits
            '/\b(\d{4,8})\b/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $code = $matches[1];
                // Validate that it's likely a verification code (4-8 digits)
                if (strlen($code) >= 4 && strlen($code) <= 8) {
                    return $code;
                }
            }
        }
        
        return null;
    }

    /**
     * Send sales notification email for USA SMS orders
     */
    private function sendSalesNotificationEmail($order)
    {
        try {
            $emailConfig = EmailConfiguration::first();
            if (!$emailConfig || !$emailConfig->email) {
                return;
            }

            $saleData = [
                'order_id' => $order->id,
                'phone_number' => $order->phone_number,
                'service_name' => $order->service->name ?? 'Unknown Service',
                'country' => $order->country->name ?? 'USA',
                'customer_name' => $order->user->name ?? 'Unknown Customer',
                'price' => $order->final_price ?? 0,
                'sale_type' => 'USA SMS Order'
            ];


            $settings = GeneralSetting::first();

            Mail::to($settings->contact_email)
                 ->queue(new SaleNotificationMail('usa_sms', $saleData, $saleData['price']));

        } catch (\Exception $e) {
            Log::error('Failed to send sales notification email for USA order ' . $order->id . ': ' . $e->getMessage());
        }
    }
}