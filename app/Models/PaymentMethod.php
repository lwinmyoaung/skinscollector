<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'paymentmethods'; // table rename လုပ်ထားရင်

    protected $fillable = [
        'name',
        'image',
        'phone_number',
    ];
}
