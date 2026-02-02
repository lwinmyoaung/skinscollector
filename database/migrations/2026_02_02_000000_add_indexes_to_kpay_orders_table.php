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
        Schema::table('kpay_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('game_type');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpay_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['game_type']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['payment_method']);
        });
    }
};
