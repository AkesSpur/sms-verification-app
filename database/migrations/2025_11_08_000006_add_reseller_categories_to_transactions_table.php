<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extend enum to include reseller categories
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
            'social_media_refund',
            'reseller_purchase',
            'reseller_refund'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove reseller categories, revert back to previous set (including social media)
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
};