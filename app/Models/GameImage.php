<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameImage extends Model
{
    protected $fillable = [
        'game_code',
        'game_name',
        'image_path',
    ];
}
