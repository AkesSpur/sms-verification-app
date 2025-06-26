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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like API responses
            $table->string('changed_by_type')->default('system'); // system, user, admin, api
            $table->unsignedBigInteger('changed_by_id')->nullable(); // user_id or admin_id
            $table->timestamp('changed_at');
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'changed_at']);
            $table->index(['status']);
            $table->index(['changed_by_type', 'changed_by_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};