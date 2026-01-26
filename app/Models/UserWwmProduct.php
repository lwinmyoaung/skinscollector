<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWwmProduct extends Model
{
    protected $table = 'userwwmproducts';

    protected $fillable = [
        'product_id',
        'category',
        'name',
        'diamonds',
        'price',
        'region',
        'status',
    ];
}
