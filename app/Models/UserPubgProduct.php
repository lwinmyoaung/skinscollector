<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPubgProduct extends Model
{
    protected $table = 'userpubgproducts';

    protected $fillable = [
        'product_id',
        'category',
        'name',
        'uc',
        'price',
        'region',
        'status',
    ];
}
