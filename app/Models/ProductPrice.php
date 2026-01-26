<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'product_prices';

    protected $fillable = [
        'product_id',
        'region',
        'name',
        'category',
        'diamonds',
        'price',
        'status',
    ];
}
