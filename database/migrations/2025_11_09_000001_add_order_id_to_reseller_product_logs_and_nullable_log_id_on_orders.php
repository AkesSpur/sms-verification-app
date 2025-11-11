<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reseller_product_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('sold_to_user_id');
            $table->foreign('order_id')
                ->references('id')->on('reseller_orders')
                ->nullOnDelete();
        });

        Schema::table('reseller_orders', function (Blueprint $table) {
            // Make log_id nullable to allow single order referencing multiple logs via logs.order_id
            if (Schema::hasColumn('reseller_orders', 'log_id')) {
                $table->unsignedBigInteger('log_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reseller_product_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });

        Schema::table('reseller_orders', function (Blueprint $table) {
            // Revert log_id to not nullable if needed
            if (Schema::hasColumn('reseller_orders', 'log_id')) {
                $table->unsignedBigInteger('log_id')->nullable(false)->change();
            }
        });
    }
};