<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'game',
        'product_id',
        'product_name',
        'player_id',
        'server_id',
        'selling_price',
        'cost_price',
        'profit',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
