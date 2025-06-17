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
        Schema::create('digital_product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('digital_products')->onDelete('cascade');
            $table->longText('log_item'); // The actual digital product content/code given to user
            $table->text('details')->nullable();
            $table->enum('status', ['available', 'sold', 'reserved', 'expired'])->default('available');
            $table->timestamp('sold_at')->nullable();
            $table->foreignId('sold_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_product_logs');
    }
};