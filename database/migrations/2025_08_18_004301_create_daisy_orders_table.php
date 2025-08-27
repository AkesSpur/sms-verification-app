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
        Schema::create('daisy_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('rental_id')->unique(); // DaisySMS rental ID
            $table->string('phone_number');
            $table->string('service_name');
            $table->string('service_code');
            $table->string('country_name');
            $table->string('country_code');
            $table->decimal('price', 28, 8);
            $table->string('trx');
            $table->string('area_codes')->nullable();
            $table->string('carrier')->nullable();
            $table->decimal('max_price', 28, 8)->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->string('sms_code')->nullable();
            $table->text('sms_text')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('rental_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daisy_orders');
    }
};