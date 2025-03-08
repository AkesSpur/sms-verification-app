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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->string('phone_number');
            $table->string('activation_id');
            $table->text('sms_code')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('refunded')->default(false);
            $table->boolean('needs_review')->default(false);
            $table->tinyInteger('retry_attempts')->default(0);
            $table->timestamp('expires_at');
            $table->boolean('is_number_used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
