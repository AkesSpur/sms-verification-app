<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test user
        $user = User::where('email', 'test@example.com')->first();
        
        if (!$user) {
            // Create a test user if it doesn't exist
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'balance' => 100.00,
                'email_verified_at' => now(),
            ]);
        }

        // Create sample transactions
        $transactions = [
            [
                'user_id' => $user->id,
                'type' => 'credit',
                'category' => 'fund_addition',
                'amount' => 50.00,
                'balance_before' => 0.00,
                'balance_after' => 50.00,
                'description' => 'Initial wallet funding',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $user->id,
                'type' => 'debit',
                'category' => 'gift_purchase',
                'amount' => 15.99,
                'balance_before' => 50.00,
                'balance_after' => 34.01,
                'description' => 'Gift purchase - Premium Gift Card',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'type' => 'debit',
                'category' => 'digital_product_purchase',
                'amount' => 9.99,
                'balance_before' => 34.01,
                'balance_after' => 24.02,
                'description' => 'Digital product purchase - Software License',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $user->id,
                'type' => 'credit',
                'category' => 'fund_addition',
                'amount' => 25.00,
                'balance_before' => 24.02,
                'balance_after' => 49.02,
                'description' => 'Wallet top-up via PayStack',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $user->id,
                'type' => 'credit',
                'category' => 'gift_refund',
                'amount' => 5.99,
                'balance_before' => 49.02,
                'balance_after' => 55.01,
                'description' => 'Refund for cancelled gift order',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Update user balance to match the last transaction
        $user->update(['balance' => 55.01]);
    }
}