<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('digital_product_orders', function (Blueprint $table) {
            $table->dropForeign(['log_id']);
            $table->foreign('log_id')->references('id')->on('digital_product_logs')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('digital_product_orders', function (Blueprint $table) {
            $table->dropForeign(['log_id']);
            $table->foreign('log_id')->references('id')->on('digital_product_logs')->onDelete('cascade');
        });
    }
};