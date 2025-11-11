<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('customer_id')->nullable()->index();
            $table->string('bank_code')->nullable();
            $table->string('account_number')->unique();
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('reserved_account_id')->nullable();
            $table->string('provider')->default('paymentpoint');
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};