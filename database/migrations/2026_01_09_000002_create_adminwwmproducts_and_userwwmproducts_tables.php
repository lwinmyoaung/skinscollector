<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('adminwwmproducts')) {
            Schema::create('adminwwmproducts', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->string('name')->nullable();
                $table->string('diamonds')->default('0');
                $table->integer('price')->default(0);
                $table->string('category')->nullable();
                $table->string('region')->default('global')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->unique(['product_id', 'region']);
            });
        }

        if (! Schema::hasTable('userwwmproducts')) {
            Schema::create('userwwmproducts', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->string('name')->nullable();
                $table->string('diamonds')->default('0');
                $table->integer('price')->default(0);
                $table->string('category')->nullable();
                $table->string('region')->default('global')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->unique(['product_id', 'region']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('userwwmproducts');
        Schema::dropIfExists('adminwwmproducts');
    }
};
