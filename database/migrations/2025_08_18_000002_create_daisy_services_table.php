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
        Schema::create('daisy_services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->index();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_popular')->default(false)->index();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daisy_services');
    }
};