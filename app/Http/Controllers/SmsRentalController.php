<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DaisyService;
use App\Models\DaisyServicePrice;
use App\Models\DaisyOrder;
use App\Models\Transaction;
use App\Services\DaisySmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SmsRentalController extends Controller
{
    protected $daisySmsService;

    public function __construct(DaisySmsService $daisySmsService)
    {
        $this->daisySmsService = $daisySmsService;
    }

    /**
     * Display SMS rental dashboard
     */
    public function index()
    {
        $pageTitle = 'SMS Number Rental';
        $user = auth()->user();
        
        $activeRentalsCount = DaisyOrder::where('user_id', $user->id)
            ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
            ->count();
            
        $rentals = DaisyOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $totalRentals = DaisyOrder::where('user_id', $user->id)->count();
        $emptyMessage = 'No SMS rentals found';
            
        $services = DaisyService::active()->with(['servicePrices' => function($q) {
            $q->where('country_code', 'us')->where('status', true);
        }])->ordered()->get();
        
        try {
            $apiBalance = $this->daisySmsService->getBalance();
        } catch (Exception $e) {
            $apiBalance = null;
        }
        
        return view( 'user.usa1.index', compact(
            'pageTitle', 'activeRentalsCount', 'rentals', 'totalRentals', 'emptyMessage', 'services', 'apiBalance'
        ));
    }

    /**
     * Rent a new number (alias for rentNumber)
     */
    public function rent(Request $request)
    {
        return $this->rentNumber($request);
    }

    /**
     * Cancel a rental (alias for cancelRental)
     */
    public function cancel(Request $request, $id)
    {
        return $this->cancelRental($id);
    }

    /**
     * View rental details
     */
    public function details($id)
    {
        $pageTitle = 'SMS Rental Details';
        $user = auth()->user();
        
        $rental = DaisyOrder::where('id', $id)
            ->where('user_id', $user->id)
            ->with('transaction')
            ->firstOrFail();
            
        return view('user.usa1.details', compact('pageTitle', 'rental'));
    }

    /**
     * Rent a new number
     */
    public function rentNumber(Request $request)
    {
        $requestId = uniqid('controller_rent_');
        $startTime = microtime(true);
        
        Log::info('SMS Rental Controller Request Started', [
            'request_id' => $requestId,
            'user_id' => auth()->user()->id,
            'request_data' => $request->only(['service', 'country', 'max_price', 'area_codes', 'carrier', 'specific_number']),
            'user_balance' => auth()->user()->balance ?? 0,
            'timestamp' => now()->toISOString(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        try {
            $request->validate([
                'service' => 'required|string',
                'max_price' => 'nullable|numeric|min:0.01|max:10',
                'area_codes' => 'nullable|string',
                'carrier' => 'nullable|in:tmo,vz,att',
                'specific_number' => 'nullable|string|regex:/^[0-9]{11}$/'
            ]);

            $user = auth()->user();
            $service = DaisyService::where('code', $request->service)->where('status', true)->first();
            
            if (!$service) {
                Log::warning('SMS Rental Failed - Invalid Service', [
                    'request_id' => $requestId,
                    'service_code' => $request->service,
                    'user_id' => $user->id
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid service selected'
                    ]);
                }
                $notify[] = ['error', 'Invalid service selected'];
                return back()->withNotify($notify);
            }
            
            Log::info('SMS Rental - Service Validated', [
                'request_id' => $requestId,
                'service_code' => $service->code,
                'service_name' => $service->name,
                'user_id' => $user->id
            ]);
            
            // Get price for the service and country
            $servicePrice = $service->getPriceForCountry($request->country ?? 'us');
            if (!$servicePrice) {
                Log::warning('SMS Rental Failed - Service Not Available for Country', [
                    'request_id' => $requestId,
                    'service_code' => $service->code,
                    'country_code' => $request->country ?? 'us',
                    'user_id' => $user->id
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Service not available for selected country'
                    ]);
                }
                $notify[] = ['error', 'Service not available for selected country'];
                return back()->withNotify($notify);
            }

            Log::info('SMS Rental - Price Check', [
                'request_id' => $requestId,
                'service_price' => $servicePrice->final_price_naira,
                'user_balance' => $user->balance,
                'sufficient_balance' => $user->balance >= $servicePrice->final_price_naira,
                'user_id' => $user->id
            ]);

            // Check if user has too many active rentals
            $activeCount = DaisyOrder::where('user_id', $user->id)
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->count();
                
            Log::info('SMS Rental - Active Rentals Check', [
                'request_id' => $requestId,
                'active_rentals' => $activeCount,
                'max_allowed' => 5,
                'can_rent' => $activeCount < 5,
                'user_id' => $user->id
            ]);
                
            if ($activeCount >= 5) {
                Log::warning('SMS Rental Failed - Max Active Rentals Reached', [
                    'request_id' => $requestId,
                    'active_rentals' => $activeCount,
                    'max_allowed' => 5,
                    'user_id' => $user->id
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can have maximum 5 active rentals at a time'
                    ]);
                }
                $notify[] = ['error', 'You can have maximum 5 active rentals at a time'];
                return back()->withNotify($notify);
            }

            // Store original balance for rollback if needed
            $originalBalance = $user->balance;
            $actualPrice = $servicePrice->final_price_naira;
            
            // Check user balance
            if ($user->balance < $actualPrice) {
                $deficit = $actualPrice - $user->balance;
                
                Log::warning('SMS Rental Failed - Insufficient Balance', [
                    'request_id' => $requestId,
                    'required_amount' => $actualPrice,
                    'user_balance' => $user->balance,
                    'deficit' => $deficit,
                    'user_id' => $user->id
                ]);
                
                $errorMessage = 'Insufficient balance. You need ' . showAmount($deficit) . ' more to complete this rental. Please deposit funds first.';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                }
                $notify[] = ['error', $errorMessage];
                return back()->withNotify($notify);
            }

            Log::info('SMS Rental - Calling DaisySMS API', [
                'request_id' => $requestId,
                'service_code' => $request->service,
                'max_price' => $request->max_price,
                'area_codes' => $request->area_codes,
                'carrier' => $request->carrier,
                'specific_number' => $request->specific_number,
                'user_id' => $user->id,
                'original_balance' => $originalBalance
            ]);

            $result = $this->daisySmsService->rentNumber(
                $request->service,
                $request->max_price,
                $request->area_codes ? explode(',', $request->area_codes) : null,
                $request->carrier,
                $request->specific_number
            );

            // Log the complete API response for observation
            Log::info('SMS Rental - FULL API RESPONSE', [
                'request_id' => $requestId,
                'complete_api_response' => $result,
                'response_keys' => array_keys($result),
                'response_structure' => [
                    'success' => $result['success'] ?? 'not_set',
                    'rental_id' => $result['rental_id'] ?? 'not_set',
                    'phone_number' => $result['phone_number'] ?? 'not_set',
                    'price' => $result['price'] ?? 'not_set',
                    'error' => $result['error'] ?? 'not_set',
                    'request_id' => $result['request_id'] ?? 'not_set'
                ],
                'service_code' => $request->service,
                'user_id' => $user->id,
                'timestamp' => now()->toISOString()
            ]);

            if (!$result['success']) {
                Log::error('SMS Rental Failed - DaisySMS API Error', [
                    'request_id' => $requestId,
                    'api_error' => $result['error'],
                    'api_request_id' => $result['request_id'] ?? null,
                    'service_code' => $request->service,
                    'user_id' => $user->id
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['error']
                    ]);
                }
                $notify[] = ['error', $result['error']];
                return back()->withNotify($notify);
            }

            Log::info('SMS Rental - DaisySMS API Success', [
                'request_id' => $requestId,
                'api_request_id' => $result['request_id'] ?? null,
                'rental_id' => $result['rental_id'],
                'phone_number' => $result['phone_number'],
                'api_price' => $result['price'] ?? 'unknown',
                'user_id' => $user->id
            ]);

            // Start database transaction for atomicity
            DB::beginTransaction();
            
            try {
                $trx = getTrx();
                
                // Set expiry time based on service type
                $expiryMinutes = 7;
                
                // Create SMS rental record first
                $rental = DaisyOrder::create([
                    'user_id' => $user->id,
                    'service_code' => $request->service,
                    'service_name' => $service->name,
                    'country_code' => $request->country ?? 'us',
                    'country_name' => $servicePrice->country_name ?? 'United States',
                    'phone_number' => $result['phone_number'],
                    'rental_id' => $result['rental_id'],
                    'price' => $actualPrice,
                    'status' => DaisyOrder::STATUS_ACTIVE,
                    'expires_at' => Carbon::now()->addMinutes($expiryMinutes), // 7 minutes 
                    'trx' => $trx,
                    'area_codes' => $request->area_codes,
                    'carrier' => $request->carrier,
                    'max_price' => $request->max_price
                ]);
                
                // Deduct from user balance using User model method
                $transaction = $user->deductBalance(
                    $actualPrice,
                    'sms_purchase',
                    'Purchased USA number for ' . $service->name,
                    $rental,
                    null
                );
                
                // Update rental with transaction ID
                $rental->update(['transaction_id' => $transaction->id]);
                
                Log::info('SMS Rental - Balance Deducted', [
                    'request_id' => $requestId,
                    'deducted_amount' => $actualPrice,
                    'new_balance' => $user->fresh()->balance,
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id
                ]);
                
                DB::commit();
                
                $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('SMS Rental Completed Successfully', [
                    'request_id' => $requestId,
                    'sms_rental_id' => $rental->id,
                    'transaction_id' => $transaction->id,
                    'processing_time_ms' => $processingTime,
                    'user_id' => $user->id,
                    'final_balance' => $user->balance
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Number rented successfully! Phone: ' . $result['phone_number'],
                        'phone_number' => $result['phone_number'],
                        'rental_id' => $rental->id
                    ]);
                }
                $notify[] = ['success', 'Number rented successfully! Phone: ' . $result['phone_number']];
                return back()->withNotify($notify);
                
            } catch (Exception $dbException) {
                DB::rollback();
                
                Log::error('SMS Rental Failed - Database Transaction Error', [
                    'request_id' => $requestId,
                    'db_exception' => $dbException->getMessage(),
                    'rental_id' => $result['rental_id'],
                    'user_id' => $user->id,
                    'stack_trace' => $dbException->getTraceAsString()
                ]);
                
                // Attempt to cancel the rental from DaisySMS since DB failed
                $cancelResult = $this->daisySmsService->cancelRental($result['rental_id']);
                
                Log::info('SMS Rental - Attempted API Cleanup After DB Failure', [
                    'request_id' => $requestId,
                    'cancel_success' => $cancelResult['success'] ?? false,
                    'cancel_message' => $cancelResult['error'] ?? $cancelResult['message'] ?? 'unknown'
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database error occurred. Please try again.'
                    ]);
                }
                $notify[] = ['error', 'Database error occurred. Please try again.'];
                return back()->withNotify($notify);
            }
            
        } catch (ValidationException $e) {
            Log::warning('SMS Rental Failed - Validation Error', [
                'request_id' => $requestId,
                'validation_errors' => $e->errors(),
                'user_id' => $user->id
            ]);
            
            if ($request->ajax()) {
                // Get the first validation error message
                $firstError = collect($e->errors())->flatten()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError ?: 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // For non-AJAX requests, add specific validation errors to notify
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $notify[] = ['error', $message];
                }
            }
            return back()->withNotify($notify)->withErrors($e->errors())->withInput();
            
        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('SMS Rental Failed - Unexpected Exception', [
                'request_id' => $requestId,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->user()->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.'
                ], 500);
            }
            $notify[] = ['error', 'Failed to rent number: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Check for SMS code
     */
    public function checkCode($id)
    {
        $requestId = uniqid('check_code_');
        $startTime = microtime(true);
        
        Log::info('SMS Check Code Request Started', [
            'request_id' => $requestId,
            'rental_id' => $id,
            'user_id' => auth()->user()->id,
            'timestamp' => now()->toISOString()
        ]);
        
        try {
            $user = auth()->user();
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            Log::info('SMS Check Code - Rental Found', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'rental_status' => $rental->status,
                'api_rental_id' => $rental->rental_id,
                'phone_number' => $rental->phone_number,
                'expires_at' => $rental->expires_at,
                'user_id' => $user->id
            ]);

            if ($rental->status !== DaisyOrder::STATUS_ACTIVE) {
                Log::warning('SMS Check Code Failed - Rental Not Active', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'current_status' => $rental->status,
                    'user_id' => $user->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Rental is not active'
                ]);
            }
            
            // Check if rental has expired with 1-minute grace period
            if ($rental->expires_at && $rental->expires_at->isPast()) {
                $minutesPastExpiry = now()->diffInMinutes($rental->expires_at);
                
                if ($minutesPastExpiry > 1) {
                    Log::warning('SMS Check Code - Rental Expired Beyond Grace Period, Auto-cancelling', [
                        'request_id' => $requestId,
                        'rental_id' => $id,
                        'expired_at' => $rental->expires_at,
                        'current_time' => now(),
                        'minutes_past_expiry' => $minutesPastExpiry,
                        'user_id' => $user->id
                    ]);
                    
                    // Auto-cancel expired rental and process refund
                    DB::beginTransaction();
                    
                    try {
                        // Update rental status to cancelled
                        $rental->update([
                            'status' => DaisyOrder::STATUS_CANCELLED,
                            'cancelled_at' => now()
                        ]);
                        
                        // Process full refund
                        $refundAmount = $rental->price;
                        $authenticatedUser = auth()->user();
                        $originalBalance = $authenticatedUser->balance;
                        
                        // Refund using User model method
                        $transaction = $authenticatedUser->addBalance(
                            $refundAmount,
                            'sms_refund',
                            'Refund for expired SMS rental - ' . $rental->service_name,
                            $rental,
                            null
                        );
                        
                        Log::info('SMS Check Code - Auto-cancel Refund Processed', [
                            'request_id' => $requestId,
                            'rental_id' => $id,
                            'original_price' => $rental->price,
                            'refund_amount' => $refundAmount,
                            'original_balance' => $originalBalance,
                            'new_balance' => $authenticatedUser->fresh()->balance,
                            'user_id' => $authenticatedUser->id,
                            'transaction_id' => $transaction->id
                        ]);
                        
                        DB::commit();
                        
                        Log::info('SMS Check Code - Auto-cancel Completed Successfully', [
                            'request_id' => $requestId,
                            'rental_id' => $id,
                            'transaction_id' => $transaction->id,
                            'user_id' => $user->id
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'This rental has expired and has been automatically cancelled. Refund: ₦' . number_format($refundAmount, 2) . ' has been credited to your account.',
                            'refunded' => true,
                            'refund_amount' => $refundAmount
                        ]);
                        
                    } catch (Exception $dbException) {
                        DB::rollback();
                        
                        Log::error('SMS Check Code - Auto-cancel Database Error', [
                            'request_id' => $requestId,
                            'rental_id' => $id,
                            'db_exception' => $dbException->getMessage(),
                            'user_id' => $user->id,
                            'stack_trace' => $dbException->getTraceAsString()
                        ]);
                        
                        // Fallback to just marking as expired
                        $rental->status = DaisyOrder::STATUS_EXPIRED;
                        $rental->save();
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'This rental has expired. Please contact support for refund assistance.'
                        ]);
                    }
                } else {
                    Log::info('SMS Check Code - Within Grace Period', [
                        'request_id' => $requestId,
                        'rental_id' => $id,
                        'expired_at' => $rental->expires_at,
                        'current_time' => now(),
                        'minutes_past_expiry' => $minutesPastExpiry,
                        'user_id' => $user->id
                    ]);
                }
            }

            Log::info('SMS Check Code - Calling DaisySMS API', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'api_rental_id' => $rental->rental_id,
                'user_id' => $user->id
            ]);

            $result = $this->daisySmsService->getCode($rental->rental_id, true);
            
            if (!$result['success']) {
                Log::warning('SMS Check Code Failed - API Error', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'api_error' => $result['error'],
                    'api_request_id' => $result['request_id'] ?? null,
                    'user_id' => $user->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ]);
            }

            if ($result['status'] === 'completed' && $result['code']) {
                Log::info('SMS Check Code - Code Received', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'api_rental_id' => $rental->rental_id,
                    'api_request_id' => $result['request_id'] ?? null,
                    'code_length' => strlen($result['code']),
                    'user_id' => $user->id
                ]);
                
                // Start database transaction
                DB::beginTransaction();
                
                try {
                    // Update rental with received code
                    $rental->update([
                        'status' => DaisyOrder::STATUS_COMPLETED,
                        'sms_code' => $result['code'],
                        'sms_text' => $result['text'],
                        'completed_at' => now()
                    ]);
                    
                    DB::commit();
                    
                    $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                    
                    Log::info('SMS Check Code Completed Successfully', [
                        'request_id' => $requestId,
                        'rental_id' => $id,
                        'processing_time_ms' => $processingTime,
                        'user_id' => $user->id
                    ]);

                    return response()->json([
                        'success' => true,
                        'status' => 'completed',
                        'code' => $result['code'],
                        'text' => $result['text'],
                        'sms_code' => $result['code'],
                        'reload' => true,
                        'message' => 'SMS code received successfully!'
                    ]);
                    
                } catch (Exception $dbException) {
                    DB::rollback();
                    
                    Log::error('SMS Check Code Failed - Database Error', [
                        'request_id' => $requestId,
                        'rental_id' => $id,
                        'db_exception' => $dbException->getMessage(),
                        'user_id' => $user->id,
                        'stack_trace' => $dbException->getTraceAsString()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Database error occurred while saving code'
                    ]);
                }
            }

            Log::info('SMS Check Code - Still Waiting', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'api_rental_id' => $rental->rental_id,
                'api_status' => $result['status'],
                'api_request_id' => $result['request_id'] ?? null,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'message' => $this->getStatusMessage($result['status'])
            ]);
            
        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('SMS Check Code Failed - Unexpected Exception', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'processing_time_ms' => $processingTime,
                'user_id' => $user->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while checking code'
            ]);
        }
    }

    /**
     * Cancel a rental
     */
    public function cancelRental($id)
    {
        $requestId = uniqid('cancel_rental_');
        $startTime = microtime(true);
        
        Log::info('SMS Cancel Rental Request Started', [
            'request_id' => $requestId,
            'rental_id' => $id,
            'user_id' => auth()->user()->id,
            'timestamp' => now()->toISOString()
        ]);
        
        try {
            $user = auth()->user();
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->firstOrFail();

            // Check if rental is past its window (more than 1 minute expired)
            if ($rental->expires_at && $rental->expires_at->isPast()) {
                $minutesPastExpiry = now()->diffInMinutes($rental->expires_at);
                
                if ($minutesPastExpiry > 1) {
                    Log::info('SMS Cancel Rental - Auto-cancelling expired rental', [
                        'request_id' => $requestId,
                        'rental_id' => $id,
                        'expired_at' => $rental->expires_at,
                        'minutes_past_expiry' => $minutesPastExpiry,
                        'user_id' => $user->id
                    ]);
                    
                    // Auto-cancel without API call
                    DB::beginTransaction();
                    
                    try {
                        $rental->update([
                            'status' => DaisyOrder::STATUS_EXPIRED,
                            'cancelled_at' => now()
                        ]);
                        
                        DB::commit();
                        
                        Log::info('SMS Cancel Rental - Auto-cancelled successfully', [
                            'request_id' => $requestId,
                            'rental_id' => $id,
                            'user_id' => $user->id
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Rental has expired and been automatically cancelled'
                        ]);
                        
                    } catch (Exception $dbException) {
                        DB::rollback();
                        
                        Log::error('SMS Cancel Rental - Auto-cancel database error', [
                            'request_id' => $requestId,
                            'rental_id' => $id,
                            'db_exception' => $dbException->getMessage(),
                            'user_id' => $user->id
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Database error occurred while auto-cancelling expired rental'
                        ]);
                    }
                }
            }

            Log::info('SMS Cancel Rental - Rental Found', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'rental_status' => $rental->status,
                'api_rental_id' => $rental->rental_id,
                'phone_number' => $rental->phone_number,
                'rental_price' => $rental->price,
                'user_id' => $user->id
            ]);

            Log::info('SMS Cancel Rental - Calling DaisySMS API', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'api_rental_id' => $rental->rental_id,
                'user_id' => auth()->id()
            ]);

            $result = $this->daisySmsService->cancelRental($rental->rental_id);
            
            if (!$result['success']) {
                Log::error('SMS Cancel Rental Failed - API Error', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'api_error' => $result['error'],
                    'api_request_id' => $result['request_id'] ?? null,
                    'user_id' => auth()->id()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel rental: ' . $result['error']
                ]);
            }
            
            Log::info('SMS Cancel Rental - API Success', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'api_rental_id' => $rental->rental_id,
                'api_request_id' => $result['request_id'] ?? null,
                'user_id' => auth()->id()
            ]);
            
            // Start database transaction
            DB::beginTransaction();
            
            try {
                // Update rental status
                $rental->update([
                    'status' => DaisyOrder::STATUS_CANCELLED,
                    'cancelled_at' => now()
                ]);
                
                // Refund user (full refund for cancellation)
                $refundAmount = $rental->price; // 100% refund
                $authenticatedUser = $user;
                $originalBalance = $authenticatedUser->balance;
                
                // Refund using User model method
                $transaction = $authenticatedUser->addBalance(
                    $refundAmount,
                    'sms_refund',
                    'Refund for cancelled SMS rental - ' . $rental->service_name,
                    $rental,
                    null
                );
                
                Log::info('SMS Cancel Rental - Refund Processed', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'original_price' => $rental->price,
                    'refund_amount' => $refundAmount,
                    'original_balance' => $originalBalance,
                    'new_balance' => $authenticatedUser->fresh()->balance,
                    'user_id' => $authenticatedUser->id,
                    'transaction_id' => $transaction->id
                ]);
                
                DB::commit();
                
                $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('SMS Cancel Rental Completed Successfully', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'transaction_id' => $transaction->id,
                    'processing_time_ms' => $processingTime,
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Rental cancelled successfully. Refund: ₦' . number_format($refundAmount, 2)
                ]);
                
            } catch (Exception $dbException) {
                DB::rollback();
                
                Log::error('SMS Cancel Rental Failed - Database Error', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'db_exception' => $dbException->getMessage(),
                    'user_id' => $user->id,
                    'stack_trace' => $dbException->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred while processing cancellation'
                ]);
            }
            
        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('SMS Cancel Rental Failed - Unexpected Exception', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'processing_time_ms' => $processingTime,
                'user_id' => $user->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while cancelling rental'
            ]);
        }
    }

    /**
     * Auto-cancel expired rental without API call
     */
    public function autoCancel($id)
    {
        $requestId = uniqid('auto_cancel_');
        $startTime = microtime(true);
        
        Log::info('SMS Auto Cancel Request Started', [
            'request_id' => $requestId,
            'rental_id' => $id,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);
        
        try {
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', auth()->id())
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->firstOrFail();

            // Check if rental is actually expired
            if (!$rental->isExpired()) {
                Log::warning('SMS Auto Cancel Failed - Rental Not Expired', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'expires_at' => $rental->expires_at,
                    'current_time' => now(),
                    'user_id' => auth()->id()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Rental has not expired yet'
                ]);
            }

            Log::info('SMS Auto Cancel - Processing Expired Rental', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'rental_status' => $rental->status,
                'expired_at' => $rental->expires_at,
                'user_id' => auth()->id()
            ]);

            // Start database transaction
            DB::beginTransaction();
            
            try {
                // Update rental status to expired (no refund for expired rentals)
                $rental->update([
                    'status' => DaisyOrder::STATUS_EXPIRED,
                    'cancelled_at' => now()
                ]);
                
                DB::commit();
                
                $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('SMS Auto Cancel Completed Successfully', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'processing_time_ms' => $processingTime,
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Rental automatically expired due to timeout'
                ]);
                
            } catch (Exception $dbException) {
                DB::rollback();
                
                Log::error('SMS Auto Cancel Failed - Database Error', [
                    'request_id' => $requestId,
                    'rental_id' => $id,
                    'db_exception' => $dbException->getMessage(),
                    'user_id' => auth()->id(),
                    'stack_trace' => $dbException->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred while processing expiration'
                ]);
            }
            
        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('SMS Auto Cancel Failed - Unexpected Exception', [
                'request_id' => $requestId,
                'rental_id' => $id,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'processing_time_ms' => $processingTime,
                'user_id' => $user->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while processing expiration'
            ]);
        }
    }

    /**
     * View rental history
     */
    public function history()
    {
        $pageTitle = 'SMS Rental History';
        $user = auth()->user();
        
        $rentals = DaisyOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('Template::user.sms_rental.history', compact('pageTitle', 'rentals'));
    }

    /**
     * Get services from database for AJAX
     */
    public function getServices()
    {
        $services = DaisyService::active()->ordered()->get(['code', 'name']);
        
        return response()->json([
            'success' => true,
            'services' => $services->map(function($service) {
                return [
                    'code' => $service->code,
                    'name' => $service->name
                ];
            })
        ]);
    }

    /**
     * Get countries for a service
     */
    public function getCountries()
    {
        // For now, return available countries from service prices
        $countries = DaisyServicePrice::active()
            ->distinct()
            ->get(['country_code', 'country_name'])
            ->map(function($price) {
                return [
                    'code' => $price->country_code,
                    'name' => $price->country_name ?: ucfirst($price->country_code)
                ];
            });
        
        return response()->json([
            'success' => true,
            'countries' => $countries
        ]);
    }

    /**
     * Get price for service and country
     */
    public function getPrice($serviceCode, $countryCode)
    {
        $service = DaisyService::where('code', $serviceCode)->where('status', true)->first();
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ]);
        }
        
        $servicePrice = $service->getPriceForCountry($countryCode);
        
        if (!$servicePrice) {
            return response()->json([
                'success' => false,
                'message' => 'Price not available for this service and country'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'price' => showAmount($servicePrice->final_price_naira),
            'price_usd' => '$' . number_format($servicePrice->final_price_usd, 2),
            'price_naira' => '₦' . number_format($servicePrice->final_price_naira, 2)
        ]);
    }

    /**
     * Get services from database for internal use
     */
    private function getServicesFromDatabase()
    {
        return DaisyService::active()->ordered()->pluck('name', 'code')->toArray();
    }

    /**
     * Get status message for display
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'waiting' => 'Waiting for SMS...',
            'cancelled' => 'Rental was cancelled',
            'completed' => 'SMS received!',
            'unknown' => 'Unknown status'
        ];
        
        return $messages[$status] ?? 'Unknown status';
    }
}