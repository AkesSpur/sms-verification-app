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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UsaNumberController extends Controller
{
    protected $smsService;
    protected $usaCountryCode = 187; // USA country code for SMS Activate
    protected $maxOrdersPerHour = 5;
    protected $maxOrdersPerDay = 20;
    protected $minBalanceRequired = 1.00;

    public function __construct(SmsActivateService $smsService)
    {
        $this->smsService = $smsService;
        $this->middleware('auth');
        $this->middleware('throttle:60,1')->only(['store', 'checkStatus']);
    }

    /**
     * Display USA numbers page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's active orders for USA numbers
        $activeOrders = Order::where('user_id', $user->id)
            ->where('country', $this->usaCountryCode)
            ->whereIn('status', ['pending', 'active', 'waiting'])
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get available services for USA using proper relationship
        $services = Service::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Get user statistics
        $stats = [
            'balance' => $user->balance ?? 0,
            'total_orders' => Order::where('user_id', $user->id)->where('country', $this->usaCountryCode)->count(),
            'active_orders' => $activeOrders->count(),
            'completed_orders' => Order::where('user_id', $user->id)
                ->where('country', $this->usaCountryCode)
                ->where('status', 'completed')
                ->count(),
        ];
        
        return view('user.usa-numbers', compact('activeOrders', 'services', 'stats'));
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

        // Rate limiting for availability checks
        $key = 'availability_check:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'available' => false,
                'message' => 'Too many requests. Please wait before checking again.'
            ], 429);
        }
        RateLimiter::hit($key, 60);

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

            if ($recentOrders >= 3) {
                return response()->json([
                    'available' => false,
                    'message' => 'You have reached the hourly limit for this service.'
                ]);
            }

            // Get price for USA
            $price = $service->getPriceForCountry($this->getUsaCountryId());
            $balance = $this->smsService->getBalance();
            // Check API availability (cached for 5 minutes)
            $cacheKey = "usa_availability_{$serviceCode}";
            $availability = Cache::remember($cacheKey, 300, function() use ($serviceCode) {
                try {
                    $result = $this->smsService->checkAvailability($serviceCode, $this->usaCountryCode);
                    return $result['available'];
                } catch (\Exception $e) {
                    Log::warning('Failed to check USA availability', [
                        'service' => $serviceCode,
                        'error' => $e->getMessage()
                    ]);
                    return false;
                }
            });

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

        // Rate limiting for purchases
        $purchaseKey = 'usa_purchase:' . $user->id;
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

                // Get price from SmsActivateService
                $price = $this->smsService->getServicePrice($serviceCode, $this->usaCountryCode);
                
                if ($user->balance < $price) {
                    throw ValidationException::withMessages([
                        'balance' => 'Insufficient balance. Required: $' . number_format($price, 2)
                    ]);
                }

                // Check for active orders
                $activeOrders = Order::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count();

                if ($activeOrders >= 3) {
                    throw ValidationException::withMessages([
                        'service' => 'You have too many active orders. Please complete or cancel existing orders first.'
                    ]);
                }

                // Request number from API
                $response = $this->smsService->purchaseNumber($serviceCode, $user->id, $this->usaCountryCode);

                if (!$response['success']) {
                    throw new \Exception('Failed to get USA number from provider.');
                }

                $order = $response['order'];
                $phoneNumber = $response['phone_number'];
                $activationId = $response['activation_id'];
                $country = $response['country'];

                // Transaction and balance deduction are handled by SmsActivateService

                // Hit rate limiter
                RateLimiter::hit($purchaseKey, 3600);

                Log::info('USA number purchased successfully', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'service' => $serviceCode,
                    'phone_number' => $phoneNumber
                ]);

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
            ->whereHas('service.countries', function($query) {
                $query->where('code', $this->usaCountryCode);
            })
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Rate limiting for status checks
        $statusKey = "status_check:{$user->id}:{$orderId}";
        if (RateLimiter::tooManyAttempts($statusKey, 30)) {
            return response()->json([
                'error' => 'Too many status checks. Please wait before checking again.'
            ], 429);
        }
        RateLimiter::hit($statusKey, 60);

        try {
            // Check if order is already completed or expired
            if (in_array($order->status, ['completed', 'expired', 'cancelled'])) {
                return response()->json([
                    'status' => $order->status,
                    'sms_code' => $order->sms_code,
                    'message' => 'Order status: ' . ucfirst($order->status)
                ]);
            }

            // Check if order has expired
            if ($order->isExpired()) {
                return $this->handleExpiredOrder($order);
            }

            // Get status from API
            $status = $this->smsService->getStatus($order->activation_id);

            return DB::transaction(function () use ($order, $status) {
                // Handle JSON response
                if (isset($status['response'])) {
                    return $this->processApiResponse($order, $status['response']);
                }

                // Handle colon-separated response
                if (isset($status['status'])) {
                    return $this->processApiStatus($order, $status['status'], $status['code'] ?? null);
                }

                // No changes
                return response()->json([
                    'status' => $order->status,
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
                'error' => 'Failed to check status. Please try again later.'
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
            ->whereHas('service.countries', function($query) {
                $query->where('countries.code', $this->usaCountryCode);
            })
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
                // Cancel with API
                $this->smsService->setStatus($order->activation_id, 8); // Cancel status

                // Update order status
                $order->update(['status' => 'cancelled']);

                // Refund if no SMS was received
                if (is_null($order->sms_code)) {
                    $service = $order->service;
                    $price = $service->getPriceForCountry($this->getUsaCountryId());
                    
                    $user->increment('balance', $price);
                    $order->update(['refunded' => true]);

                    // Create refund transaction
                    Transaction::createTransaction(
                        $user,
                        'credit',
                        'sms_refund',
                        $price,
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
                        'refund_amount' => $price
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order cancelled and refunded successfully.',
                        'refund_amount' => $price
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
            ->whereHas('service.countries', function($query) {
                $query->where('code', $this->usaCountryCode);
            })
            ->with(['service', 'user'])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

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
            ->where('status', 'expired')
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
                'balance' => 'Minimum balance of $' . number_format($this->minBalanceRequired, 2) . ' required.'
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
                'status' => 'expired',
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
            $order->update(['status' => 'expired']);
            return response()->json([
                'status' => 'expired',
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if (strpos($response, 'STATUS_OK') !== false) {
            $code = explode(':', $response)[1] ?? null;
            if ($code) {
                $order->update([
                    'sms_code' => $code,
                    'status' => 'completed'
                ]);

                Log::info('USA SMS code received', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id
                ]);

                return response()->json([
                    'status' => 'completed',
                    'sms_code' => $code,
                    'message' => 'SMS code received successfully!'
                ]);
            }
        }

        return response()->json([
            'status' => $order->status,
            'message' => 'Waiting for SMS...'
        ]);
    }

    /**
     * Process API status
     */
    private function processApiStatus(Order $order, string $status, ?string $code = null)
    {
        if ($status === 'STATUS_CANCEL') {
            $order->update(['status' => 'expired']);
            return response()->json([
                'status' => 'expired',
                'message' => 'Order was cancelled by provider.'
            ]);
        }

        if ($status === 'STATUS_OK' && $code) {
            $order->update([
                'sms_code' => $code,
                'status' => 'completed'
            ]);

            Log::info('USA SMS code received', [
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);

            return response()->json([
                'status' => 'completed',
                'sms_code' => $code,
                'message' => 'SMS code received successfully!'
            ]);
        }

        return response()->json([
            'status' => $order->status,
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
}