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
        Schema::create('localbanks', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->text('extra_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localbanks');
    }
};