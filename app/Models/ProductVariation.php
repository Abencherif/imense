<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $table = 'product_variations';
    protected $fillable =[
        'id',
        'product_id',
        'external_id',
        'color',
        'material',
        'quantity',
        'additional_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}
