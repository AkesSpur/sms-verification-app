<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use App\Models\User;
use App\Models\Transaction;
use App\Models\BlacklistedNumber;
use App\Services\SmsActivateService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InternationalNumberController extends Controller
{
    protected $smsService;
    protected $pricingService;
    protected $maxOrdersPerHour = 5;
    protected $maxOrdersPerDay = 20;
    protected $minBalanceRequired = 100.00; // Minimum balance in Naira

    /**
     * Get country mapping for SMS Activate API
     */
    private function getCountryMapping($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        return $country ? $country->code : null;
    }

    /**
     * Get all country mappings (cached for performance)
     */
    private function getCountryMappings()
    {
        return Cache::remember('country_mappings', 3600, function () {
            return Country::pluck('code', 'code')->toArray();
        });
    }

    public function __construct(SmsActivateService $smsService, PricingService $pricingService)
    {
        $this->smsService = $smsService;
        $this->pricingService = $pricingService;
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
        $countryMappings = $this->getCountryMappings();
        
        $request->validate([
            'country' => 'required|string|in:' . implode(',', array_keys($countryMappings)),
            'service' => 'required|string'
        ]);

        $countryCode = $request->input('country');
        $serviceCode = $request->input('service');
        $user = Auth::user();

        try {
            // Get country code for SMS Activate API
            $apiCountryCode = $this->getCountryMapping($countryCode);
            
            if (!$apiCountryCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country not supported'
                ], 400);
            }
            
            // Check if service exists and is active
            $service = Service::where('code', $serviceCode)
                ->where('status', 'active')
                ->first();

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'available' => false,
                    'message' => 'Service not available for this country.'
                ]);
            }

            // Get price for this country and service
            $countryModel = Country::where('code', $countryCode)->first();
            if (!$countryModel) {
                return response()->json([
                    'success' => false,
                    'available' => false,
                    'message' => 'Country not supported.'
                ]);
            }

            $priceInNaira = $this->pricingService->getServicePrice($service->id, $countryModel->id);
            
            // Check user balance
            if ($user->balance < $priceInNaira) {
                return response()->json([
                    'success' => false,
                    'available' => false,
                    'message' => 'Insufficient balance. Required: ₦' . number_format($priceInNaira, 2)
                ]);
            }

            // Check availability from SMS Activate API
            $availability = $this->smsService->checkAvailability($serviceCode, $apiCountryCode);
            
            if ($availability['available']) {
                return response()->json([
                    'success' => true,
                    'available' => true,
                    'message' => 'Service is available!',
                    'price' => $priceInNaira,
                    'formatted_price' => '₦' . number_format($priceInNaira, 2)
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'available' => false,
                    'message' => 'Service temporarily unavailable. Please try again later.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('International number availability check failed', [
                'user_id' => $user->id,
                'country' => $countryCode,
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
        $countryMappings = $this->getCountryMappings();
        
        $request->validate([
            'country' => 'required|string|in:' . implode(',', array_keys($countryMappings)),
            'service' => 'required|string'
        ]);

        $countryCode = $request->input('country');
        $serviceCode = $request->input('service');
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
            return DB::transaction(function () use ($request, $countryCode, $serviceCode, $user) {
                // Get API country code
                $apiCountryCode = $this->getCountryMapping($countryCode);
                
                if (!$apiCountryCode) {
                    throw new \Exception('Country not supported');
                }
                
                // Get service with lock
                $service = Service::where('code', $serviceCode)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if (!$service) {
                    throw ValidationException::withMessages([
                        'service' => 'Service not available for international numbers.'
                    ]);
                }

                // Get country model
                $countryModel = Country::where('code', $countryCode)->first();
                if (!$countryModel) {
                    throw ValidationException::withMessages([
                        'country' => 'Country not supported.'
                    ]);
                }

                // Get price in Naira from PricingService
                $priceInNaira = $this->pricingService->getServicePrice($service->id, $countryModel->id);
                
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

                // Request number from API
                $response = $this->smsService->purchaseNumber($serviceCode, $user->id, $apiCountryCode, $priceInNaira, 'international_numbers_web');

                if (!$response['success']) {
                    throw new \Exception('Failed to get international number from provider.');
                }

                $order = $response['order'];
                $phoneNumber = $response['phone_number'];
                $activationId = $response['activation_id'];
                $country = $response['country'];

                // Hit rate limiter (skip for admin users)
                if ($user->role !== 'admin') {
                    $purchaseKey = "international_purchase:{$user->id}";
                    RateLimiter::hit($purchaseKey, 3600);
                }

                Log::info('International number purchased successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'country' => $countryCode,
                    'service' => $serviceCode,
                    'phone_number' => $phoneNumber
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'International number purchased successfully!',
                    'order' => [
                        'id' => $order->id,
                        'phone_number' => $phoneNumber,
                        'service' => $service->name,
                        'country' => $countryCode,
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
                'country' => $countryCode,
                'service' => $serviceCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase number. Please try again later.'
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
            return DB::transaction(function () use ($order, $user, $orderId) {
                // Check if order should be auto-cancelled
                if ($order->shouldBeAutoCancelled()) {
                    $order->cancel();
                    
                    return response()->json([
                        'success' => true,
                        'order' => [
                            'status' => Order::STATUS_CANCELLED,
                            'sms_code' => null,
                            'phone_number' => $order->phone_number
                        ],
                        'message' => 'Order automatically cancelled due to SMS timeout. Refund has been processed.'
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

                // Get status from API
                $status = $this->smsService->getStatus($order->activation_id);

                // Handle JSON response
                if (isset($status['response'])) {
                    return $this->processApiResponse($order, $status['response']);
                }

                // Handle colon-separated response
                if (isset($status['status'])) {
                    return $this->processApiStatus($order, $status['status'], $status['code'] ?? null);
                }

                // Handle legacy success format
                if (isset($status['success']) && $status['success'] && isset($status['sms_code'])) {
                    $order->update([
                        'sms_code' => $status['sms_code'],
                        'sms_received_at' => Carbon::now(),
                        'status' => Order::STATUS_COMPLETED
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'order' => [
                            'status' => Order::STATUS_COMPLETED,
                            'sms_code' => $status['sms_code'],
                            'phone_number' => $order->phone_number,
                            'expires_at' => $order->sms_window_expires_at ? $order->sms_window_expires_at->toISOString() : null
                        ],
                        'message' => 'SMS code received!'
                    ]);
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
            $code = explode(':', $response)[1] ?? null;
            if ($code) {
                $order->update([
                    'sms_code' => $code,
                    'sms_received_at' => Carbon::now(),
                    'status' => Order::STATUS_COMPLETED
                ]);

                Log::info('International SMS code received', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id
                ]);

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
    public function cancelOrder(Request $request, $orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACTIVE])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or cannot be cancelled'], 404);
        }

        // Check if order can be cancelled (within 20 minutes)
        if ($order->created_at->diffInMinutes(now()) > 20) {
            return response()->json([
                'error' => 'Order cannot be cancelled after 20 minutes'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($order, $user) {
                // Try to cancel with the SMS provider if we have an activation ID
                if ($order->activation_id && $this->smsService) {
                    try {
                        $this->smsService->setStatus($order->activation_id, 8); // 8 = cancel
                    } catch (\Exception $e) {
                        Log::warning('Failed to cancel order with SMS provider', [
                            'order_id' => $order->id,
                            'activation_id' => $order->activation_id,
                            'error' => $e->getMessage()
                        ]);
                    }
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
}