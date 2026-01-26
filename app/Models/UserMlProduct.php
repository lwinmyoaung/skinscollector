<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMlProduct extends Model
{
    protected $table = 'usermlproducts';

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
