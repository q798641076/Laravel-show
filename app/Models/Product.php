<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;

class Product extends Model
{
    protected $fillable=[
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $cast=[
        'on_sale'=>'boolean'
    ];

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
