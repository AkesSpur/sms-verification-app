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
        Schema::table('country_service', function (Blueprint $table) {
            // Add availability tracking per country-service combination
            $table->boolean('is_available')->default(true)->after('is_active');
            $table->integer('available_numbers')->default(0)->after('is_available');
            $table->timestamp('last_availability_check')->nullable()->after('available_numbers');
            
            // Add pricing details
            $table->decimal('api_price', 8, 2)->nullable()->after('price');
            $table->decimal('markup_percentage', 5, 2)->nullable()->after('api_price');
            $table->decimal('final_price', 8, 2)->nullable()->after('markup_percentage');
            $table->timestamp('last_price_update')->nullable()->after('final_price');
            
            // Add service statistics per country
            $table->integer('total_orders')->default(0)->after('last_price_update');
            $table->integer('successful_orders')->default(0)->after('total_orders');
            $table->integer('failed_orders')->default(0)->after('successful_orders');
            $table->decimal('success_rate', 5, 2)->default(0)->after('failed_orders');
            
            // Add configuration per country-service
            $table->integer('max_daily_orders')->nullable()->after('success_rate');
            $table->integer('max_hourly_orders')->nullable()->after('max_daily_orders');
            $table->decimal('min_balance_required', 8, 2)->default(1.00)->after('max_hourly_orders');
            
            // Add status tracking
            $table->enum('status', [
                'active',           // Available for orders
                'inactive',         // Temporarily disabled
                'maintenance',      // Under maintenance
                'out_of_stock',     // No numbers available
                'suspended'         // Suspended due to issues
            ])->default('active')->after('min_balance_required');
            
            // Add API response tracking
            $table->text('last_api_response')->nullable()->after('status');
            $table->timestamp('last_api_check')->nullable()->after('last_api_response');
            
            // Add indexes for better performance
            $table->index(['is_active', 'is_available', 'status']);
            $table->index(['country_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index(['last_availability_check']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_service', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['is_active', 'is_available', 'status']);
            $table->dropIndex(['country_id', 'status']);
            $table->dropIndex(['service_id', 'status']);
            $table->dropIndex(['last_availability_check']);
            
            // Drop new columns
            $table->dropColumn([
                'is_available',
                'available_numbers',
                'last_availability_check',
                'api_price',
                'markup_percentage',
                'final_price',
                'last_price_update',
                'total_orders',
                'successful_orders',
                'failed_orders',
                'success_rate',
                'max_daily_orders',
                'max_hourly_orders',
                'min_balance_required',
                'status',
                'last_api_response',
                'last_api_check'
            ]);
        });
    }
};