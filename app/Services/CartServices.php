<?php
namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartServices
{
    public function get()
    {
        //获取购物车列表
        return Auth::user()->cartItems()->with('productSku.product')->get();
    }

    public function add($skuId,$amount)
    {
        //添加购物车
        $user=Auth::user();
        //如果购物车已有，则++
        if($cartItems=$user->cartItems()->where('product_sku_id',$skuId)->first()){

            $cartItems->update([
                'amount'=>$cartItems->amount+$amount
            ]);

        }else{
            //创建新的item
            $cartItems=new CartItem([
                'amount'=>$amount
            ]);
            $cartItems->user()->associate(Auth::user());
            $cartItems->productSku()->associate($skuId);
            $cartItems->save();
        }
        return $cartItems;
    }

    public function remove($skuId)
    {
        //可以传一个id也可以传数组
        if(!is_array($skuId)){
            $skuId=[$skuId];
        }

        $cartItems=Auth::user()->cartItems()->whereIn('product_sku_id',$skuId)->delete();

        return $cartItems;
    }
}
