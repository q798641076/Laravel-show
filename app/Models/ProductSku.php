<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class ProductSku extends Model
{
    protected $fillable=[
        'title','description',
        'stock','price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
