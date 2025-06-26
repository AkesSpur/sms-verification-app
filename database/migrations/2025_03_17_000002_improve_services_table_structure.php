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
        Schema::table('services', function (Blueprint $table) {
            // Improve status tracking with more specific statuses
            $table->dropColumn('status');
            $table->enum('status', [
                'active',           // Service is available
                'inactive',         // Service is temporarily disabled
                'maintenance',      // Service is under maintenance
                'deprecated',       // Service is being phased out
                'unavailable'       // Service is not available from provider
            ])->default('active')->after('allow_refunds');
            
            // Add service availability tracking
            $table->boolean('is_available')->default(true)->after('status');
            $table->integer('available_numbers')->default(0)->after('is_available');
            $table->timestamp('last_availability_check')->nullable()->after('available_numbers');
            
            // Add service configuration
            $table->integer('max_retry_attempts')->default(3)->after('last_availability_check');
            $table->integer('sms_timeout_minutes')->default(20)->after('max_retry_attempts');
            $table->boolean('auto_refund_on_timeout')->default(true)->after('sms_timeout_minutes');
            
            // Add API configuration
            $table->string('api_service_code')->nullable()->after('code');
            $table->json('api_config')->nullable()->after('auto_refund_on_timeout');
            
            // Add pricing configuration
            $table->decimal('base_price', 8, 2)->default(0)->after('price');
            $table->decimal('markup_percentage', 5, 2)->default(20.00)->after('base_price');
            $table->boolean('use_dynamic_pricing')->default(false)->after('markup_percentage');
            
            // Add service statistics
            $table->integer('total_orders')->default(0)->after('use_dynamic_pricing');
            $table->integer('successful_orders')->default(0)->after('total_orders');
            $table->decimal('success_rate', 5, 2)->default(0)->after('successful_orders');
            
            // Add service metadata
            $table->text('description')->nullable()->after('name');
            $table->string('icon')->nullable()->after('description');
            $table->integer('sort_order')->default(0)->after('icon');
            
            // Add indexes for better performance
            $table->index(['status', 'is_available']);
            $table->index(['code']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['status', 'is_available']);
            $table->dropIndex(['code']);
            $table->dropIndex(['sort_order']);
            
            // Drop new columns
            $table->dropColumn([
                'is_available',
                'available_numbers',
                'last_availability_check',
                'max_retry_attempts',
                'sms_timeout_minutes',
                'auto_refund_on_timeout',
                'api_service_code',
                'api_config',
                'base_price',
                'markup_percentage',
                'use_dynamic_pricing',
                'total_orders',
                'successful_orders',
                'success_rate',
                'description',
                'icon',
                'sort_order'
            ]);
            
            // Restore old status column
            $table->string('status')->default('active')->after('allow_refunds');
        });
    }
};