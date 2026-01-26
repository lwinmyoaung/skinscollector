<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_images', function (Blueprint $table) {
            $table->id();
            $table->string('game_code')->unique();
            $table->string('game_name');
            $table->string('image_path');
            $table->timestamps();
        });

        // Insert default data
        DB::table('game_images')->insert([
            [
                'game_code' => 'mlbb',
                'game_name' => 'Mobile Legend:Bang Bang',
                'image_path' => 'photo/sora.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_code' => 'pubg',
                'game_name' => 'PUBG Mobile (UC)',
                'image_path' => 'photo/pubg.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_code' => 'mcgg',
                'game_name' => 'Magic Chess GoGo',
                'image_path' => 'photo/mcgg.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_code' => 'wwm',
                'game_name' => 'Where Winds Meet',
                'image_path' => 'photo/wwm.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_images');
    }
};
