<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPubgProduct extends Model
{
    protected $table = 'adminpubgproducts';

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
