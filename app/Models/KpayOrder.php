<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpayOrder extends Model
{
    protected $fillable = [
        'user_id',
        'game_type',
        'product_id',
        'product_name',
        'player_id',
        'server_id',
        'region',
        'payment_method',
        'kpay_phone',
        'amount',
        'transaction_image',
        'status',
    ];
}
