<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kpay_orders') && ! Schema::hasColumn('kpay_orders', 'payment_method')) {
            Schema::table('kpay_orders', function (Blueprint $table) {
                $table->string('payment_method')->default('kpay')->after('region');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kpay_orders') && Schema::hasColumn('kpay_orders', 'payment_method')) {
            Schema::table('kpay_orders', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }
};

