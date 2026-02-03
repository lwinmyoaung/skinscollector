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

    public function getImageUrlAttribute()
    {
        if (!$this->image_path || $this->image_path === '0') {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($this->image_path, 'game_images')) {
            return asset('storage/' . $this->image_path);
        }
        return asset('adminimages/' . $this->image_path);
    }
}
