<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ProductSku;

class CartItem extends Model
{
    protected $fillable=['amount'];

    //数据表中如果没有timestamp字段就要启用此项，因为创建数据的时候会自动加入时间戳，除了多对多关联
    public $timestamps=false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
