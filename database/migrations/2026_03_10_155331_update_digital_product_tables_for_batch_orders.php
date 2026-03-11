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
        // Add order_id to digital_product_logs table to link multiple logs to one order
        Schema::table('digital_product_logs', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('product_id')->constrained('digital_product_orders')->nullOnDelete();
        });

        // Make log_id nullable in digital_product_orders table since new orders will link from the log side
        Schema::table('digital_product_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('log_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_product_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });

        Schema::table('digital_product_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('log_id')->nullable(false)->change();
        });
    }
};
