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
        Schema::table('orders', function (Blueprint $table) {
            
            // Add proper country foreign key
            $table->foreignId('country_id')->after('service_id')->constrained('countries')->onDelete('cascade');
            
            // Improve status tracking with more specific statuses
            $table->dropColumn('status');
            $table->enum('status', [
                'pending',           // Order created, waiting for SMS
                'active',           // Number assigned, waiting for SMS
                'completed',        // SMS received successfully
                'expired',          // 20-minute window expired
                'cancelled',        // Manually cancelled by user
                'failed',           // API error or other failure
                'refunded'          // Refund processed
            ])->default('pending')->after('activation_id');
            
            // Add SMS window tracking
            $table->timestamp('sms_window_expires_at')->nullable()->after('expires_at');
            $table->timestamp('sms_received_at')->nullable()->after('sms_code');
            
            // Add API response tracking
            $table->text('api_response')->nullable()->after('sms_received_at');
            $table->string('api_status')->nullable()->after('api_response');
            
            // Add cancellation tracking
            $table->boolean('can_cancel')->default(true)->after('refunded');
            $table->timestamp('cancelled_at')->nullable()->after('can_cancel');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            
            // Add order source tracking
            $table->string('order_source')->default('web')->after('cancellation_reason'); // web, api, mobile
            
            // Add pricing details
            $table->decimal('api_price', 8, 2)->nullable()->after('price');
            $table->decimal('markup_percentage', 5, 2)->nullable()->after('api_price');
            $table->decimal('final_price', 8, 2)->nullable()->after('markup_percentage');
            
            // Add retry tracking improvements
            $table->timestamp('last_retry_at')->nullable()->after('retry_attempts');
            $table->tinyInteger('max_retries')->default(3)->after('last_retry_at');
            
            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['expires_at']);
            $table->index(['sms_window_expires_at']);
            $table->index(['country_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop new columns and indexes
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['sms_window_expires_at']);
            $table->dropIndex(['country_id', 'service_id']);
            
            $table->dropForeign(['country_id']);
            $table->dropColumn([
                'country_id',
                'sms_window_expires_at',
                'sms_received_at',
                'api_response',
                'api_status',
                'can_cancel',
                'cancelled_at',
                'cancellation_reason',
                'order_source',
                'api_price',
                'markup_percentage',
                'final_price',
                'last_retry_at',
                'max_retries'
            ]);
            
            // Restore old status column
            $table->string('status')->default('pending')->after('activation_id');
            
            // Restore old country column
            $table->string('country', 10)->nullable()->after('service_id');
            $table->index('country');
        });
    }
};