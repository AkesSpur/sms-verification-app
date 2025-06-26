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
            if (!Schema::hasColumn('general_settings', 'naira_to_dollar_rate')) {
                $table->decimal('naira_to_dollar_rate', 8, 2)->default(1700.00)->after('enable_dynamic_pricing');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'naira_to_dollar_rate')) {
                $table->dropColumn(['naira_to_dollar_rate']);
            }
        });
    }
};
