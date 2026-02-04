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
        'quantity',
        'transaction_image',
        'status',
    ];

    public function getTransactionImageUrlAttribute()
    {
        if (!$this->transaction_image) {
            return asset('adminimages/default.jpg');
        }
        
        // Since we moved to adminimages disk, all paths should be prefixed with adminimages/
        // The stored path is like 'topups/filename.jpg'.
        if (\Illuminate\Support\Str::startsWith($this->transaction_image, 'topups/')) {
            return asset('adminimages/' . $this->transaction_image);
        }
        
        return asset('adminimages/topups/' . $this->transaction_image);
    }
}
