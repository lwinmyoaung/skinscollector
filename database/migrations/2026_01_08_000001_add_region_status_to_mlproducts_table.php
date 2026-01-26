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
        if (! Schema::hasTable('mlproducts')) {
            Schema::create('mlproducts', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->string('name')->nullable();
                $table->string('diamonds')->default('0');
                $table->integer('price')->default(0);
                $table->string('category')->nullable();
                $table->string('region')->default('myanmar')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->unique(['product_id', 'region']);
            });
        } else {
            Schema::table('mlproducts', function (Blueprint $table) {
                if (! Schema::hasColumn('mlproducts', 'region')) {
                    $table->string('region')->default('myanmar')->index();
                }
                if (! Schema::hasColumn('mlproducts', 'status')) {
                    $table->boolean('status')->default(true)->index();
                }
                $table->unique(['product_id', 'region']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mlproducts')) {
            Schema::table('mlproducts', function (Blueprint $table) {
                if (Schema::hasColumn('mlproducts', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('mlproducts', 'region')) {
                    $table->dropColumn('region');
                }
                $table->dropUnique(['product_id', 'region']);
            });
        }
    }
};
