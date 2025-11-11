<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Services\PaymentPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentPointController extends Controller
{
    public function getVirtualAccount(Request $request)
    {
        $user = $request->user();
        $va = VirtualAccount::where('user_id', $user->id)->first();

        if (!$va) {
            return response()->json(['success' => true, 'has_account' => false]);
        }

        return response()->json([
            'success' => true,
            'has_account' => true,
            'account' => [
                'account_number' => $va->account_number,
                'account_name' => $va->account_name,
                'bank_name' => $va->bank_name,
                'bank_code' => $va->bank_code,
                'reserved_account_id' => $va->reserved_account_id,
            ],
        ]);
    }

    public function createVirtualAccount(Request $request, PaymentPointService $service)
    {
        $user = $request->user();

        // If already has, return existing
        $existing = VirtualAccount::where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Virtual account already exists',
                'account' => [
                    'account_number' => $existing->account_number,
                    'account_name' => $existing->account_name,
                    'bank_name' => $existing->bank_name,
                    'bank_code' => $existing->bank_code,
                ],
            ]);
        }

        // Call service
        $result = $service->createVirtualAccount($user);
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Failed to create account'], 422);
        }

        $data = $result['data'];
        $customerId = data_get($data, 'customer.customer_id');
        $account = data_get($data, 'bankAccounts.0');

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'No account returned from provider'], 422);
        }

        $va = new VirtualAccount();
        $va->user_id = $user->id;
        $va->customer_id = $customerId;
        $va->bank_code = $account['bankCode'] ?? null;
        $va->account_number = $account['accountNumber'] ?? '';
        $va->account_name = $account['accountName'] ?? null;
        $va->bank_name = $account['bankName'] ?? null;
        $va->reserved_account_id = $account['Reserved_Account_Id'] ?? null;
        $va->provider = 'paymentpoint';
        $va->raw_response = $data;
        $va->save();

        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Virtual account created',
            'account' => [
                'account_number' => $va->account_number,
                'account_name' => $va->account_name,
                'bank_name' => $va->bank_name,
                'bank_code' => $va->bank_code,
            ],
        ]);
    }

    /**
     * Webhook endpoint for PaymentPoint notifications.
     * Verifies signature, logs payload, credits user balance and creates transaction.
     */
    public function webhook(Request $request)
    {
        $raw = $request->getContent();
        $headerSignature = $request->header('Paymentpoint-Signature')
            ?? $request->header('PAYMENTPOINT_SIGNATURE')
            ?? $request->server('HTTP_PAYMENTPOINT_SIGNATURE');

        $secret = config('services.paymentpoint.webhook_secret');
        $calculated = hash_hmac('sha256', $raw, $secret);

        // Log incoming webhook
        Log::channel('paymentpoint')->info('Incoming webhook', [
            'headers' => $request->headers->all(),
            'payload' => json_decode($raw, true),
        ]);

        if (!$headerSignature || !hash_equals($calculated, $headerSignature)) {
            Log::channel('paymentpoint')->warning('Invalid signature', [
                'provided' => $headerSignature,
                'calculated' => $calculated,
            ]);
            return response('Invalid signature', 400);
        }

        $data = json_decode($raw, true);
        if ($data === null) {
            Log::channel('paymentpoint')->error('Invalid JSON payload');
            return response('Invalid JSON data received.', 400);
        }

        $status = data_get($data, 'transaction_status');
        $notificationStatus = data_get($data, 'notification_status');
        $transactionId = data_get($data, 'transaction_id');
        $settlementAmount = (float) (data_get($data, 'settlement_amount') ?? 0);
        $amountPaid = (float) (data_get($data, 'amount_paid') ?? 0);
        $creditAmount = $settlementAmount > 0 ? $settlementAmount : $amountPaid;

        if (!$transactionId || !$creditAmount || !$status) {
            Log::channel('paymentpoint')->error('Missing required data', compact('transactionId', 'creditAmount', 'status'));
            return response('Missing required data.', 400);
        }

        // Identify the user via customer_id or receiver.account_number or email
        $customerId = data_get($data, 'customer.customer_id');
        $receiverAccount = data_get($data, 'receiver.account_number');
        $email = data_get($data, 'customer.email');

        $va = null;
        if ($customerId) {
            $va = VirtualAccount::where('customer_id', $customerId)->first();
        }
        if (!$va && $receiverAccount) {
            $va = VirtualAccount::where('account_number', $receiverAccount)->first();
        }

        $user = $va?->user ?? ($email ? User::where('email', $email)->first() : null);

        if (!$user) {
            Log::channel('paymentpoint')->error('User not found for webhook', [
                'customer_id' => $customerId,
                'receiver_account' => $receiverAccount,
                'email' => $email,
            ]);
            return response('User not found.', 404);
        }

        try {
            DB::transaction(function () use ($user, $creditAmount, $data) {
                // Create transaction log BEFORE balance update so balance_before is correct
                Transaction::createTransaction(
                    $user,
                    'credit',
                    'fund_addition',
                    $creditAmount,
                    'PaymentPoint Virtual Account Funding',
                    [
                        'provider' => 'paymentpoint',
                        'payload' => $data,
                        'paymentpoint_transaction_id' => data_get($data, 'transaction_id'),
                        'settlement_fee' => data_get($data, 'settlement_fee'),
                        'bank' => [
                            'sender' => data_get($data, 'sender'),
                            'receiver' => data_get($data, 'receiver'),
                        ],
                    ],
                    reference: null,
                    admin: null,
                    paymentMethod: 'paymentpoint'
                );

                // Update user balance
                $user->increment('balance', $creditAmount);
            });
        } catch (\Throwable $e) {
            Log::channel('paymentpoint')->error('Funding failed', ['error' => $e->getMessage()]);
            return response('Processing failed.', 500);
        }

        Log::channel('paymentpoint')->info('Funding completed', [
            'user_id' => $user->id,
            'amount' => $creditAmount,
            'transaction_id' => $transactionId,
        ]);

        return response()->json(['success' => true, 'message' => 'Webhook processed successfully.']);
    }
}