<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Unique transaction identifier
            $table->enum('type', ['credit', 'debit']); // Credit (money in) or Debit (money out)
            $table->enum('category', [
                'fund_addition',     // Admin adding funds
                'fund_withdrawal',   // Admin withdrawing funds
                'gift_purchase',     // User purchasing gifts
                'gift_refund',       // Gift order refunds
                'digital_purchase',  // Digital product purchases
                'digital_refund',    // Digital product refunds
                'sms_purchase',      // SMS service purchases
                'sms_refund'         // SMS service refunds
            ]);
            $table->decimal('amount', 15, 2); // Transaction amount
            $table->decimal('balance_before', 15, 2); // User balance before transaction
            $table->decimal('balance_after', 15, 2); // User balance after transaction
            $table->string('description'); // Transaction description
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->string('reference_type')->nullable(); // Model type (Order, GiftOrder, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Model ID
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin who performed the action
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'category']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};