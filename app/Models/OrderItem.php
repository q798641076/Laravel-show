<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;

class OrderItem extends Model
{
    protected $fillable=[
        'amount','price','rating','review', 'reviewed_at'
    ];

    protected $dates = ['reviewed_at'];

    public $timestamps=false;

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function product_sku(){
        return $this->belongsTo(ProductSku::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
