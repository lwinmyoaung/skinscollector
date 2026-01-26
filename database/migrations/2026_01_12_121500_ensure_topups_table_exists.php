<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('topups')) {
            Schema::create('topups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('amount');
                $table->string('payment_method');
                $table->string('transaction_image');
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('topups')) {
            Schema::dropIfExists('topups');
        }
    }
};
