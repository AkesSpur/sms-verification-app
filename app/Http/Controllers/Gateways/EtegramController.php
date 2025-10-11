<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Etegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EtegramController extends Controller
{

    public function etegramRedirect(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'amount' => 'required|numeric|min:100|max:1000000',
                'user_id' => 'required|exists:users,id'
            ]);

            $amount = $request->input('amount');
            $userId = $request->input('user_id');
            
            // Store amount in session for later use in verification
            session(['deposit_amount' => $amount]);

            // Check if user is authenticated
            if (!Auth::check()) {
                toastr()->error('Please login to continue');
                return redirect()->route('login');
            }

            // Get Etegram configuration
            $etegramConfig = Etegram::getActiveConfig();
            if (!$etegramConfig) {
                toastr()->error('Etegram payment gateway is not configured. Please contact support.');
                return redirect()->route('user.transaction');
            }

            $user = Auth::user();
            $email = $user->email;
            
            // Extract user's first and last name from the name field or use defaults
            $nameParts = explode(' ', $user->name ?? 'User Name', 2);
            $firstName = $nameParts[0] ?? 'User';
            $lastName = $nameParts[1] ?? 'Name';

            // Etegram API endpoint for transaction initialization (using merchant_id)
            $url = 'https://api-checkout.etegram.com/api/transaction/initialize/' . $etegramConfig->merchant_id;
            $headers = [
                // 'Authorization' => 'Bearer ' . $etegramConfig->public_key,
                'Authorization' => 'Bearer pk_live-8f369e47704244ff852dee6d3dc08163',
                'Content-Type' => 'application/json',
                'Content-Length' => 0, // Will be set automatically by Laravel HTTP client
            ];
            // Generate a unique reference for this transaction
            $reference = 'ETG_' . strtoupper(Str::random(8)) . '_' . time();
            
            $data = [
                'amount' => $amount,
                'email' => $email,
                'phone' => $user->phone ?? '080*******', // Use user's phone or default
                'firstname' => $firstName,
                'lastname' => $lastName,
                'reference' => $reference,
            ];

            $response = Http::withHeaders($headers)->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['authorization_url']) && isset($responseData['data']['access_code'])) {
                    // Store access code in session for verification
                    session(['etegram_access_code' => $responseData['data']['access_code']]);
                    return redirect($responseData['data']['authorization_url']);
                } else {
                    // Log the error response for debugging
                    Log::error('Etegram API error: ' . $response->body());
                    toastr()->error('Failed to get payment URL from Etegram. Please try again.');
                    return redirect()->route('user.transaction');
                }
            } else {
                // Log the error response for debugging
                Log::error('Etegram API error: ' . $response->body());
                toastr()->error('Failed to initiate your payment with Etegram, try again. If issue keeps occurring, contact support for help.');
                return redirect()->route('user.transaction');
            }
        } catch (\Exception $e) {
            toastr()->error('An error occurred while processing your payment. Please try again.');
            return redirect()->route('user.transaction');
        }
    }

    public function verifyTransaction(Request $request)
    {
        $accessCode = $request->access_code ?? session('etegram_access_code');
        
        if (!$accessCode) {
            toastr()->error('Invalid transaction access code');
            return redirect()->route('user.transaction');
        }
        
        return DB::transaction(function () use ($accessCode) {
            // Check if transaction with this access code already exists
            $existingTransaction = Transaction::where('reference', $accessCode)->first();
            
            if ($existingTransaction) {
                if ($existingTransaction->status == 'completed') {
                    // Transaction already processed successfully - prevent duplicate processing
                    toastr()->info('This transaction has already been processed successfully.');
                    session()->forget(['deposit_amount', 'etegram_access_code']);
                    return redirect()->route('user.transaction');
                }
                
                if ($existingTransaction->status == 'failed') {
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
                    $pendingTransaction->reference = $accessCode;
                    $pendingTransaction->type = 'credit';
                    $pendingTransaction->category = 'fund_addition';
                    $pendingTransaction->amount = $amount;
                    $pendingTransaction->balance_before = $user->balance;
                    $pendingTransaction->balance_after = $user->balance; // Will be updated after verification
                    $pendingTransaction->description = 'Etegram deposit - ' . $accessCode;
                    $pendingTransaction->email = $user->email;
                    $pendingTransaction->payment_method = 'Etegram';
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
            
            // Get Etegram configuration
            $etegramConfig = Etegram::getActiveConfig();
            if (!$etegramConfig) {
                toastr()->error('Etegram payment gateway is not configured. Please contact support.');
                return redirect()->route('user.transaction');
            }
            
            // Etegram API endpoint for transaction verification (using PATCH method as per documentation)
            $url = "https://api-checkout.etegram.com/api/transaction/verify-payment/{$etegramConfig->merchant_id}/{$accessCode}";
        
            // Use raw cURL as per Etegram sample code
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                // toastr()->error('Payment verification failed: ' . $error);
                // return redirect()->route('user.transaction');
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo '<pre>';
            echo "HTTP Code: " . $httpCode . "\n";
            echo "Response: " . $response . "\n";
            die;

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === true) {

                    $user = Auth::user();
                    $depositAmount = $data['data']['amount'] ?? $pendingTransaction->amount;
                    $newBalance = $user->balance + $depositAmount;

                    // update user balance
                    $user->update([
                        'balance' => $newBalance
                    ]);

                    // Update the pending transaction with complete details
                    $pendingTransaction->transaction_id = $data['data']['id'] ?? $pendingTransaction->transaction_id;
                    $pendingTransaction->amount = $depositAmount;
                    $pendingTransaction->balance_after = $newBalance;
                    $pendingTransaction->status = 'completed';
                    $pendingTransaction->save();

                    toastr()->success('₦' . number_format($depositAmount, 2) . ' was successfully deposited into your account via Etegram');
                    session()->forget(['deposit_amount', 'etegram_access_code']);

                    return redirect()->route('user.transaction');

                } else {
                    // Update transaction record for failed transaction
                    $pendingTransaction->transaction_id = $data['data']['id'] ?? $pendingTransaction->transaction_id;
                    $pendingTransaction->amount = $data['data']['amount'] ?? session('deposit_amount', 0);
                    $pendingTransaction->status = 'failed';
                    $pendingTransaction->save();
                    
                    toastr()->error('Etegram deposit was unsuccessful. Contact support if you have complaints');
                    session()->forget(['deposit_amount', 'etegram_access_code']);

                    return redirect()->route('user.transaction');
                }
            } else {
                // Update transaction record for API error
                $pendingTransaction->amount = session('deposit_amount') ?? 0;
                $pendingTransaction->status = 'failed';
                $pendingTransaction->save();

                toastr()->error('An error occurred with Etegram verification. Contact support if you have complaints');
                session()->forget(['deposit_amount', 'etegram_access_code']);
            
                return redirect()->route('user.transaction');
            }
        });
    }
}