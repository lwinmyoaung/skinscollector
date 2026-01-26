<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWwmProduct extends Model
{
    protected $table = 'adminwwmproducts';

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
