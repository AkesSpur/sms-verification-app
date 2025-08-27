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
        Schema::create('daisy_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('daisy_services')->onDelete('cascade');
            $table->string('country_code', 5)->index();
            $table->string('country_name')->nullable();
            $table->decimal('price_usd', 10, 4)->default(0);
            $table->decimal('price_naira', 10, 2)->default(0);
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->boolean('status')->default(true)->index();
            $table->string('api_price_id')->nullable();
            $table->timestamp('last_updated_from_api')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint to prevent duplicate service-country combinations
            $table->unique(['service_id', 'country_code']);
            
            // Indexes for better performance
            $table->index(['service_id', 'status']);
            $table->index(['country_code', 'status']);
            $table->index(['price_naira', 'status']);
            $table->index(['service_id', 'country_code', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daisy_service_prices');
    }
};