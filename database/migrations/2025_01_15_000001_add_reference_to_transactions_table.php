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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('transaction_id'); // Paystack reference
            $table->string('email')->nullable()->after('reference'); // User email at time of transaction
            $table->string('payment_method')->nullable()->after('email'); // Payment method used
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reference', 'email', 'payment_method']);
        });
    }
};