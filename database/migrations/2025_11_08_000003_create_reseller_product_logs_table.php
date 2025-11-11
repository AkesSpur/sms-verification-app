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
        Schema::create('reseller_product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('reseller_products')->onDelete('cascade');
            $table->text('log_item');
            $table->text('details')->nullable();
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->timestamp('sold_at')->nullable();
            $table->foreignId('sold_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('sold_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_product_logs');
    }
};