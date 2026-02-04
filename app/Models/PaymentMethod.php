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

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('adminimages/default_payment.jpg');
        }
        
        if (\Illuminate\Support\Str::startsWith($this->image, 'payment_methods/')) {
            return asset('adminimages/' . $this->image);
        }
        
        return asset('adminimages/images/paymentmethodphoto/' . $this->image);
    }
}
