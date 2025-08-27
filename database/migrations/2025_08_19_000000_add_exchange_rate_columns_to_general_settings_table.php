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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->decimal('usd_to_ngn_rate', 10, 4)->nullable()->after('naira_to_dollar_rate');
            $table->timestamp('exchange_rate_updated_at')->nullable()->after('usd_to_ngn_rate');
            $table->decimal('exchange_rate_markup_percentage', 5, 2)->default(0)->after('exchange_rate_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['usd_to_ngn_rate', 'exchange_rate_updated_at', 'exchange_rate_markup_percentage']);
        });
    }
};