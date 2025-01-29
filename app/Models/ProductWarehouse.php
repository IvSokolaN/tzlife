<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    protected $fillable = [
        'quantity',
        'product_id',
        'warehouse_id',
    ];


}
