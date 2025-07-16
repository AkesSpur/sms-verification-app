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
        Schema::table('social_media_orders', function (Blueprint $table) {
            $table->string('external_order_id')->nullable()->after('admin_notes');
            $table->string('external_status')->nullable()->after('external_order_id');
            $table->integer('external_start_count')->nullable()->after('external_status');
            $table->integer('external_remains')->nullable()->after('external_start_count');
            $table->decimal('external_charge', 10, 2)->nullable()->after('external_remains');
            
            $table->index('external_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_media_orders', function (Blueprint $table) {
            $table->dropIndex(['external_order_id']);
            $table->dropColumn([
                'external_order_id',
                'external_status',
                'external_start_count',
                'external_remains',
                'external_charge'
            ]);
        });
    }
};