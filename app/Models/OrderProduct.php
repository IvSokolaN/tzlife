<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'quantity',
        'price',
        'order_id',
        'product_id',
    ];
}
