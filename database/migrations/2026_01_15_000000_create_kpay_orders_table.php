<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpay_orders', function (Blueprint $table) {
            $table->id();
            $table->string('game_type');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('player_id');
            $table->string('server_id')->nullable();
            $table->string('region')->nullable();
            $table->string('kpay_phone');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_image');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpay_orders');
    }
};

