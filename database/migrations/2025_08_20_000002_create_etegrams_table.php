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
        Schema::create('etegrams', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(false);
            $table->string('country_name');
            $table->string('currency_name');
            $table->text('public_key');
            $table->text('secret_key')->nullable();
            $table->string('merchant_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etegrams');
    }
};