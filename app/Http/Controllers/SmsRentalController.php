<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DaisyOrder;
use App\Models\GeneralSetting;
use App\Services\GetATextService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class SmsRentalController extends Controller
{
    protected GetATextService $getATextService;

    public function __construct(GetATextService $getATextService)
    {
        $this->getATextService = $getATextService;
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Convert a USD price to Naira using GeneralSetting rate + markup,
     * rounded up to the nearest ₦10.
     */
    private function toNaira(float $usdPrice): float
    {
        $settings = GeneralSetting::first();
        $rate     = (float) ($settings->usd_to_ngn_rate ?? 1600);
        $markup   = (float) ($settings->api_price_markup_percentage ?? 0);

        return (float) ceil(($usdPrice * $rate * (1 + $markup / 100)) / 10) * 10;
    }

    // ─────────────────────────────────────────────────────────────
    // Pages
    // ─────────────────────────────────────────────────────────────

    /**
     * Display SMS rental dashboard
     */
    public function index()
    {
        $pageTitle = 'SMS Number Rental';
        $user      = auth()->user();

        $activeRentalsCount = DaisyOrder::where('user_id', $user->id)
            ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
            ->count();

        $rentals = DaisyOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalRentals = DaisyOrder::where('user_id', $user->id)->count();
        $emptyMessage = 'No SMS rentals found';

        // Services come from GetAText API (cached 1 hr); each entry has
        // 'name', 'short_name', 'cost' (USD), 'count' (stock), 'price' (USD), 'ttl'
        $services = $this->getATextService->getServices();

        try {
            $apiBalance = $this->getATextService->getBalance();
        } catch (Exception $e) {
            $apiBalance = null;
        }

        return view('user.usa1.index', compact(
            'pageTitle',
            'activeRentalsCount',
            'rentals',
            'totalRentals',
            'emptyMessage',
            'services',
            'apiBalance'
        ));
    }

    /**
     * View rental details
     */
    public function details($id)
    {
        $pageTitle = 'SMS Rental Details';
        $user      = auth()->user();

        $rental = DaisyOrder::where('id', $id)
            ->where('user_id', $user->id)
            ->with('transaction')
            ->firstOrFail();

        return view('user.usa1.details', compact('pageTitle', 'rental'));
    }

    // ─────────────────────────────────────────────────────────────
    // Aliases
    // ─────────────────────────────────────────────────────────────

    public function rent(Request $request)
    {
        return $this->rentNumber($request);
    }

    public function cancel(Request $request, $id = null)
    {
        $id = $id ?? $request->route('id');
        return $this->cancelRental($id);
    }

    // ─────────────────────────────────────────────────────────────
    // Core actions
    // ─────────────────────────────────────────────────────────────

    /**
     * Rent a new number via GetAText API
     */
    public function rentNumber(Request $request)
    {
        $requestId = uniqid('rent_');
        $startTime = microtime(true);

        try {
            $request->validate([
                'service'   => 'required|string',
                'max_price' => 'nullable|numeric|min:0.01|max:10',
            ]);

            $user    = auth()->user();
            $rateKey = 'sms_rent:' . $user->id;

            if (RateLimiter::tooManyAttempts($rateKey, 1)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please wait a moment before submitting another rental request.',
                    ], 429);
                }
                $notify[] = ['error', 'Please wait a moment before submitting another rental request.'];
                return back()->withNotify($notify);
            }
            RateLimiter::hit($rateKey, 3);

            // ── Validate service against cached GetAText list ──
            $cachedServices = collect($this->getATextService->getServices());
            $serviceEntry   = $cachedServices->firstWhere('short_name', $request->service);

            if (!$serviceEntry) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Invalid service selected']);
                }
                $notify[] = ['error', 'Invalid service selected'];
                return back()->withNotify($notify);
            }

            if ((int) $serviceEntry['count'] <= 0) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'This service is currently out of stock. Please try another service.']);
                }
                $notify[] = ['error', 'This service is currently out of stock. Please try another service.'];
                return back()->withNotify($notify);
            }

            // ── Compute Naira price ──
            $actualPrice = $this->toNaira((float) $serviceEntry['cost']);

            Log::channel('getatext')->info('Rent attempt', [
                'request_id'  => $requestId,
                'service'     => $request->service,
                'usd_price'   => $serviceEntry['cost'],
                'naira_price' => $actualPrice,
                'user_id'     => $user->id,
            ]);

            // ── Active rentals limit ──
            $activeCount = DaisyOrder::where('user_id', $user->id)
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->count();

            if ($activeCount >= 5) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'You can have maximum 5 active rentals at a time']);
                }
                $notify[] = ['error', 'You can have maximum 5 active rentals at a time'];
                return back()->withNotify($notify);
            }

            // ── Balance check ──
            if ($user->balance < $actualPrice) {
                $deficit      = $actualPrice - $user->balance;
                $errorMessage = 'Insufficient balance. You need ' . showAmount($deficit) . ' more to complete this rental. Please deposit funds first.';

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage]);
                }
                $notify[] = ['error', $errorMessage];
                return back()->withNotify($notify);
            }

            // ── Call GetAText API ──
            $result = $this->getATextService->rentNumber(
                $request->service,
                $request->max_price ? (float) $request->max_price : null
            );

            if (!$result['success']) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $result['error']]);
                }
                $notify[] = ['error', $result['error']];
                return back()->withNotify($notify);
            }

            Log::channel('getatext')->info('Rent API success', [
                'request_id'   => $requestId,
                'rental_id'    => $result['rental_id'],
                'phone_number' => $result['phone_number'],
                'user_id'      => $user->id,
            ]);

            // ── Save to DB ──
            DB::beginTransaction();

            try {
                $trx = getTrx();

                $rental = DaisyOrder::create([
                    'user_id'      => $user->id,
                    'service_code' => $request->service,
                    'service_name' => $result['service_name'],
                    'country_code' => 'us',
                    'country_name' => 'United States',
                    'phone_number' => $result['phone_number'],
                    'rental_id'    => (string) $result['rental_id'],
                    'price'        => $actualPrice,
                    'status'       => DaisyOrder::STATUS_ACTIVE,
                    'expires_at'   => $result['expires_at'],
                    'trx'          => $trx,
                    'max_price'    => $request->max_price,
                ]);

                $transaction = $user->deductBalance(
                    $actualPrice,
                    'sms_purchase',
                    'Purchased USA number for ' . $result['service_name'],
                    $rental,
                    null
                );

                $rental->update(['transaction_id' => $transaction->id]);

                DB::commit();

                $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::channel('getatext')->info('Rent completed', [
                    'request_id'      => $requestId,
                    'order_id'        => $rental->id,
                    'transaction_id'  => $transaction->id,
                    'processing_ms'   => $processingTime,
                    'user_id'         => $user->id,
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success'      => true,
                        'message'      => 'Number rented successfully! Phone: ' . $result['phone_number'],
                        'phone_number' => $result['phone_number'],
                        'rental_id'    => $rental->id,
                    ]);
                }
                $notify[] = ['success', 'Number rented successfully! Phone: ' . $result['phone_number']];
                return back()->withNotify($notify);

            } catch (Exception $dbException) {
                DB::rollBack();

                Log::channel('getatext')->error('Rent DB error — attempting API cleanup', [
                    'request_id' => $requestId,
                    'rental_id'  => $result['rental_id'],
                    'error'      => $dbException->getMessage(),
                ]);

                // Best-effort cancel on GetAText side
                $this->getATextService->cancelRental((int) $result['rental_id']);

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Database error occurred. Please try again.']);
                }
                $notify[] = ['error', 'Database error occurred. Please try again.'];
                return back()->withNotify($notify);
            }

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                $firstError = collect($e->errors())->flatten()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError ?: 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            foreach ($e->errors() as $messages) {
                foreach ($messages as $message) {
                    $notify[] = ['error', $message];
                }
            }
            return back()->withNotify($notify)->withErrors($e->errors())->withInput();

        } catch (Exception $e) {
            Log::channel('getatext')->error('Rent unexpected exception', [
                'request_id' => $requestId,
                'error'      => $e->getMessage(),
                'user_id'    => auth()->id(),
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An unexpected error occurred. Please try again.'], 500);
            }
            $notify[] = ['error', 'Failed to rent number: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Check for SMS code (webhook-driven: reads from DB, no API polling)
     */
    public function checkCode($id)
    {
        try {
            $user   = auth()->user();
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // ── Terminal status — nothing more to do ──
            if (in_array($rental->status, [DaisyOrder::STATUS_CANCELLED, DaisyOrder::STATUS_EXPIRED])) {
                return response()->json([
                    'success' => false,
                    'status'  => $rental->status,
                    'message' => 'Rental has been ' . $rental->status . '.',
                ]);
            }

            // ── Code check FIRST — never cancel an order that already has a code ──
            $rental->refresh();
            if ($rental->sms_code) {
                // If the order expired while holding a code, complete it instead of cancelling
                if (in_array($rental->status, [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                    && $rental->isExpired()
                ) {
                    try {
                        $rental->update(['status' => DaisyOrder::STATUS_COMPLETED, 'completed_at' => now()]);
                        $rental->refresh();
                    } catch (Exception $e) {
                        // non-fatal — code is still returned below
                    }
                }

                return response()->json([
                    'success'  => true,
                    'status'   => $rental->status,
                    'sms_code' => $rental->sms_code,
                    'code'     => $rental->sms_code,
                    'text'     => $rental->sms_text,
                    'message'  => 'SMS code: ' . $rental->sms_code,
                ]);
            }

            // ── No code: if expired beyond 1-minute grace period — auto-cancel with refund ──
            if ($rental->expires_at && $rental->expires_at->isPast()) {
                $minutesPastExpiry = max(0, now()->timestamp - $rental->expires_at->timestamp) / 60;

                if ($minutesPastExpiry > 1) {
                    DB::beginTransaction();
                    try {
                        $rental->update([
                            'status'       => DaisyOrder::STATUS_CANCELLED,
                            'cancelled_at' => now(),
                        ]);

                        $refundAmount = $rental->price;
                        $transaction  = $user->addBalance(
                            $refundAmount,
                            'sms_refund',
                            'Refund for expired SMS rental - ' . $rental->service_name,
                            $rental,
                            null
                        );

                        DB::commit();

                        return response()->json([
                            'success'       => false,
                            'message'       => 'This rental has expired and has been automatically cancelled. Refund: ₦' . number_format($refundAmount, 2) . ' has been credited to your account.',
                            'refunded'      => true,
                            'refund_amount' => $refundAmount,
                        ]);

                    } catch (Exception $dbException) {
                        DB::rollBack();
                        $rental->status = DaisyOrder::STATUS_EXPIRED;
                        $rental->save();

                        return response()->json([
                            'success' => false,
                            'message' => 'This rental has expired. Please contact support for refund assistance.',
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'status'  => 'waiting',
                'message' => 'Waiting for SMS code...',
            ]);

        } catch (Exception $e) {
            Log::channel('getatext')->error('checkCode exception', [
                'rental_id' => $id,
                'error'     => $e->getMessage(),
                'user_id'   => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while checking code',
            ]);
        }
    }

    /**
     * Cancel a rental (GetAText requires ≥1 min since creation)
     */
    public function cancelRental($id)
    {
        $requestId = uniqid('cancel_');
        $startTime = microtime(true);

        try {
            $user   = auth()->user();
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->firstOrFail();

            // ── Cannot cancel an order that has already received a code ──
            if ($rental->sms_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel an order after receiving a code.',
                ]);
            }

            // ── Expired beyond grace period — mark as expired, no API call ──
            if ($rental->expires_at && $rental->expires_at->isPast()) {
                $minutesPastExpiry = max(0, now()->timestamp - $rental->expires_at->timestamp) / 60;

                if ($minutesPastExpiry > 1) {
                    DB::beginTransaction();
                    try {
                        $rental->update([
                            'status'       => DaisyOrder::STATUS_EXPIRED,
                            'cancelled_at' => now(),
                        ]);
                        DB::commit();

                        return response()->json([
                            'success' => true,
                            'message' => 'Rental has expired and been automatically cancelled',
                        ]);
                    } catch (Exception $dbException) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Database error occurred while auto-cancelling expired rental',
                        ]);
                    }
                }
            }

            // ── GetAText requires ≥1 minute after creation before cancelling ──
            $secondsSinceCreation = max(0, now()->timestamp - $rental->created_at->timestamp);
            if ($secondsSinceCreation < 60) {
                $waitSeconds = 60 - $secondsSinceCreation;
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$waitSeconds} more second(s) before cancelling.",
                ]);
            }

            // ── Call GetAText cancel API ──
            $result = $this->getATextService->cancelRental((int) $rental->rental_id);

            if (!$result['success']) {
                // 404 = rental no longer exists on GetAText (already expired/viewed on their side).
                // Treat as "provider already cleaned up" and cancel locally with refund.
                $providerAlreadyGone = str_contains(strtolower($result['error']), 'not found');

                if (!$providerAlreadyGone) {
                    Log::channel('getatext')->error('Cancel API error', [
                        'request_id' => $requestId,
                        'rental_id'  => $rental->rental_id,
                        'error'      => $result['error'],
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to cancel rental: ' . $result['error'],
                    ]);
                }

                Log::channel('getatext')->info('Cancel: rental already gone on provider side, cancelling locally', [
                    'request_id' => $requestId,
                    'rental_id'  => $rental->rental_id,
                ]);
            }

            // ── Persist cancellation and refund ──
            DB::beginTransaction();

            try {
                $rental->update([
                    'status'       => DaisyOrder::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                ]);

                $refundAmount = $rental->price;
                $transaction  = $user->addBalance(
                    $refundAmount,
                    'sms_refund',
                    'Refund for cancelled SMS rental - ' . $rental->service_name,
                    $rental,
                    null
                );

                DB::commit();

                $processingTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::channel('getatext')->info('Cancel completed', [
                    'request_id'     => $requestId,
                    'order_id'       => $rental->id,
                    'refund_amount'  => $refundAmount,
                    'transaction_id' => $transaction->id,
                    'processing_ms'  => $processingTime,
                    'user_id'        => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Rental cancelled successfully. Refund: ₦' . number_format($refundAmount, 2),
                ]);

            } catch (Exception $dbException) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred while processing cancellation',
                ]);
            }

        } catch (Exception $e) {
            Log::channel('getatext')->error('Cancel unexpected exception', [
                'request_id' => $requestId,
                'rental_id'  => $id,
                'error'      => $e->getMessage(),
                'user_id'    => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while cancelling rental',
            ]);
        }
    }

    /**
     * Auto-cancel expired rental without API call (called from frontend timer)
     */
    public function autoCancel($id)
    {
        try {
            $rental = DaisyOrder::where('id', $id)
                ->where('user_id', auth()->id())
                ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
                ->firstOrFail();

            if (!$rental->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rental has not expired yet',
                ]);
            }

            // ── Orders with a code are completed, not expired ──
            if ($rental->sms_code) {
                DB::beginTransaction();
                try {
                    $rental->update(['status' => DaisyOrder::STATUS_COMPLETED, 'completed_at' => now()]);
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'Rental marked as completed']);
                } catch (Exception $dbException) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Database error occurred while processing completion']);
                }
            }

            DB::beginTransaction();
            try {
                $rental->update([
                    'status'       => DaisyOrder::STATUS_EXPIRED,
                    'cancelled_at' => now(),
                ]);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Rental automatically expired due to timeout',
                ]);
            } catch (Exception $dbException) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred while processing expiration',
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while processing expiration',
            ]);
        }
    }

    /**
     * View rental history
     */
    public function history()
    {
        $pageTitle = 'SMS Rental History';
        $user      = auth()->user();

        $rentals = DaisyOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Template::user.sms_rental.history', compact('pageTitle', 'rentals'));
    }

    // ─────────────────────────────────────────────────────────────
    // AJAX helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Return available services with stock info (from GetAText cache)
     */
    public function getServices()
    {
        $services = $this->getATextService->getServices();

        return response()->json([
            'success'  => true,
            'services' => array_map(fn ($s) => [
                'code'     => $s['short_name'],
                'name'     => $s['name'],
                'stock'    => $s['count'],
                'in_stock' => $s['count'] > 0,
                'price'    => $this->toNaira((float) $s['cost']),
            ], $services),
        ]);
    }

    /**
     * Return countries (GetAText only supports US numbers)
     */
    public function getCountries()
    {
        return response()->json([
            'success'   => true,
            'countries' => [
                ['code' => 'us', 'name' => 'United States'],
            ],
        ]);
    }

    /**
     * Get price for a service (computed from cached API data)
     */
    public function getPrices($serviceCode, $countryCode)
    {
        $services     = collect($this->getATextService->getServices());
        $serviceEntry = $services->firstWhere('short_name', $serviceCode);

        if (!$serviceEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found',
            ]);
        }

        $priceNaira = $this->toNaira((float) $serviceEntry['cost']);

        return response()->json([
            'success'     => true,
            'price'       => showAmount($priceNaira),
            'price_usd'   => '$' . number_format($serviceEntry['cost'], 2),
            'price_naira' => '₦' . number_format($priceNaira, 2),
            'stock'       => $serviceEntry['count'],
        ]);
    }
}
