<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $table = 'product_variations';
    protected $fillable =[
        'id',
        'product_id',
        'attribute_name',
        'attribute_value',
        'price'
    ];

}
