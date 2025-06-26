<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Paystack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaystackController extends Controller
{

    public function paystackRedirect()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            toastr()->error('Please login to continue');
            return redirect()->route('login');
        }

        // Get Paystack configuration
        $paystackConfig = Paystack::getActiveConfig();
        if (!$paystackConfig) {
            toastr()->error('Payment gateway is not configured. Please contact support.');
            return redirect()->route('user.transaction');
        }

        $amount = session('deposit_amount');
        if (!$amount || $amount <= 0) {
            toastr()->error('Invalid deposit amount');
            return redirect()->route('user.transaction');
        }

        $amount = $amount * 100; // Amount in kobo
        $email = Auth::user()->email;

        $url = 'https://api.paystack.co/transaction/initialize';
        $headers = [
            'Authorization' => 'Bearer ' . $paystackConfig->secret_key,
            'Content-Type' => 'application/json', 
        ];
        $data = [
            'amount' => $amount,
            'email' => $email,
            'callback_url' => route('user.paystack.callback'),
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->successful()) {
            $data = $response->json();
            return redirect($data['data']['authorization_url']);
        } else {
            // Handle error
            toastr()->error('Failed to initiate your payment, try again. If issue keeps occurring, contact support for help.');
            return redirect()->route('user.transaction');
        }
    }


    public function verifyTransaction(Request $request)
    {
        // echo  session('deposit_amount');
        // die;

        $reference = $request->reference;
        
        return DB::transaction(function () use ($reference) {
            // Check if transaction with this reference already exists
            $existingTransaction = Transaction::where('reference', $reference)->first();
            
            if ($existingTransaction) {
                if ($existingTransaction->status === 'completed') {
                    // Transaction already processed successfully - prevent duplicate processing
                    toastr()->info('This transaction has already been processed successfully.');
                    session()->forget('deposit_amount');
                    return redirect()->route('user.transaction');
                }
                
                if ($existingTransaction->status === 'failed') {
                    // Transaction already failed - allow retry by updating status to pending
                    $existingTransaction->status = 'pending';
                    $existingTransaction->save();
                    $pendingTransaction = $existingTransaction;
                } else {
                    // Transaction is pending - prevent concurrent processing
                    toastr()->info('Your transaction is being processed. Please wait.');
                    return redirect()->route('user.transaction');
                }
            } else {
                // Create a new pending transaction record
                try {
                    $user = Auth::user();
                    $amount = session('deposit_amount');
                    
                    $pendingTransaction = new Transaction();
                    $pendingTransaction->user_id = $user->id;
                    $pendingTransaction->transaction_id = 'TXN' . strtoupper(Str::random(8)) . time();
                    $pendingTransaction->reference = $reference;
                    $pendingTransaction->type = 'credit';
                    $pendingTransaction->category = 'fund_addition';
                    $pendingTransaction->amount = $amount;
                    $pendingTransaction->balance_before = $user->balance;
                    $pendingTransaction->balance_after = $user->balance; // Will be updated after verification
                    $pendingTransaction->description = 'Paystack deposit - ' . $reference;
                    $pendingTransaction->email = $user->email;
                    $pendingTransaction->payment_method = 'Paystack';
                    $pendingTransaction->status = 'pending';
                    $pendingTransaction->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle unique constraint violation (race condition)
                    if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                        toastr()->info('This transaction is already being processed.');
                        return redirect()->route('user.transaction');
                    }
                    throw $e;
                }
            }
            
            // Get Paystack configuration
            $paystackConfig = Paystack::getActiveConfig();
            if (!$paystackConfig) {
                toastr()->error('Payment gateway is not configured. Please contact support.');
                return redirect()->route('user.transaction');
            }
            
            $url = "https://api.paystack.co/transaction/verify/$reference";
            $headers = [
                'Authorization' => 'Bearer ' . $paystackConfig->secret_key,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->get($url);  

            if ($response->successful()) {
                $data = $response->json();
                if ($data['data']['status'] === 'success') {

                $user = Auth::user();
                $depositAmount = $data['data']['amount']/100;
                $newBalance = $user->balance + $depositAmount;

                // update user balance
                $user->update([
                    'balance' => $newBalance
                ]);

                // Update the pending transaction with complete details
                $pendingTransaction->transaction_id = $data['data']['id'];
                $pendingTransaction->amount = $depositAmount;
                $pendingTransaction->balance_after = $newBalance;
                $pendingTransaction->status = 'completed';
                $pendingTransaction->save();
                
                toastr()->success( $data['data']['amount']/100 . ' was successfully deposit into your account');
                session()->forget('deposit_amount');

                return redirect()->route('user.transaction');

                } else {
                // Update transaction record for failed transaction
                $pendingTransaction->transaction_id = $data['data']['id'];
                $pendingTransaction->amount = $data['data']['amount']/100;
                $pendingTransaction->status = 'failed';
                $pendingTransaction->save();
                
                toastr()->error('Deposit was unsuccessful. Contact support if you have complains');
                session()->forget('deposit_amount');

                return redirect()->route('user.transaction');
                }
            } else {
                // Update transaction record for API error
                $pendingTransaction->amount = session('deposit_amount') ?? 0;
                $pendingTransaction->status = 'failed';
                $pendingTransaction->save();

                toastr()->error('An error occurred. Contact support if you have complains');
                session()->forget('deposit_amount');
            
                return redirect()->route('user.transaction');
            }
        });
    }
    }




