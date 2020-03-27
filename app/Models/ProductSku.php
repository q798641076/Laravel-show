<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Exceptions\InternalException;

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

        //减库存  适合高并发
        public function decreaseStock($amount)
        {
            if($amount<0){
                throw new InternalException('库存不能减小于0');
            }
            //where('id',$this->id)这个条件要加上去，因为他不会自动查询自己本身的
            return $this->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);
        }
        //加库存
        public function addStock($amount)
        {
            if($amount<0){
                throw new InternalException('库存不能加小于0');
            }
            return $this->increment('stock',$amount);
        }
}
