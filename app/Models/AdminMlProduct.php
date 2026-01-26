<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMlProduct extends Model
{
    protected $table = 'adminmlproducts';

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
