<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to alter the enum column to add new values
        DB::statement("ALTER TABLE transactions MODIFY COLUMN category ENUM(
            'fund_addition',
            'fund_withdrawal',
            'gift_purchase',
            'gift_refund',
            'digital_purchase',
            'digital_refund',
            'sms_purchase',
            'sms_refund',
            'social_media_purchase',
            'social_media_refund'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE transactions MODIFY COLUMN category ENUM(
            'fund_addition',
            'fund_withdrawal',
            'gift_purchase',
            'gift_refund',
            'digital_purchase',
            'digital_refund',
            'sms_purchase',
            'sms_refund'
        )");
    }
};