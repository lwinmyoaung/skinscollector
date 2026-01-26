<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('region');
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->integer('diamonds')->default(0);
            $table->integer('price')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->unique(['product_id', 'region']);
            $table->index(['region', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
