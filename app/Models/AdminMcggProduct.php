<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMcggProduct extends Model
{
    protected $table = 'adminmcggproducts';

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
