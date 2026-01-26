<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $table = 'topups';
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'transaction_image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
