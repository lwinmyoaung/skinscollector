<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMcggProduct extends Model
{
    protected $table = 'usermcggproducts';

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
