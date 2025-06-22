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
        Schema::create('gift_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gift_id')->constrained('gifts')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('customization_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'failed'])->default('pending');
            $table->enum('payment_method', ['wallet', 'card', 'bank_transfer'])->default('wallet');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('order_number')->unique();
            $table->timestamp('ordered_at')->nullable();
            
            // Recipient Information
            $table->string('recipient_name');
            $table->string('recipient_phone');
            
            // Sender Information
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('sender_email');
            
            // Delivery Address
            $table->text('delivery_address');
            $table->string('delivery_apartment')->nullable(); // Apartment/Suite (Optional)
            $table->string('delivery_city');
            $table->string('delivery_state');
            $table->string('delivery_country')->default('Nigeria');
            $table->string('delivery_zip')->nullable();
            
            // Customization
            $table->boolean('is_customized')->default(false);
            $table->string('custom_image')->nullable();
            $table->text('custom_message')->nullable();
            $table->text('gift_message')->nullable();
            
            // Tracking
            $table->longText('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['gift_id', 'status']);
            $table->index('order_number');
            $table->index('ordered_at');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_orders');
    }
};