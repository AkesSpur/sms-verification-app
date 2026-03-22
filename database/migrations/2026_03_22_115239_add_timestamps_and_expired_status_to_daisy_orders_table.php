<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daisy_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('daisy_orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('daisy_orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('cancelled_at');
            }
        });

        // Widen the status enum to include 'expired'
        DB::statement("ALTER TABLE daisy_orders MODIFY COLUMN status
                       ENUM('pending','active','completed','cancelled','expired')
                       NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('daisy_orders', function (Blueprint $table) {
            $cols = array_filter(['cancelled_at', 'completed_at'],
                fn($c) => Schema::hasColumn('daisy_orders', $c));
            if ($cols) {
                $table->dropColumn(array_values($cols));
            }
        });

        DB::statement("ALTER TABLE daisy_orders MODIFY COLUMN status
                       ENUM('pending','active','completed','cancelled')
                       NOT NULL DEFAULT 'pending'");
    }
};
