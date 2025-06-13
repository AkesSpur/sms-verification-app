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
            $table->decimal('api_price_markup_percentage', 5, 2)->default(20.00)->after('site_name');
            $table->boolean('enable_dynamic_pricing')->default(true)->after('api_price_markup_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['api_price_markup_percentage', 'enable_dynamic_pricing']);
        });
    }
};