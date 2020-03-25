<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductSku;

class CartsController extends Controller
{
    //添加购物车
    public function addCart(AddCartRequest $request)
    {
        $user=$request->user();
        $skuId=$request->sku_id;
        $amount=$request->amount;

        //如果商品存在
        if($cart=$user->cartItems()->where('product_sku_id',$skuId)->first())
        {
            //添加表中的amount字段
            $cart->amount+=$amount;

            $cart->save();

            return [];

        }

        //否则添加商品
        $cart=new CartItem(['amount'=>$request->amount]);
        $cart->user_id=$user->id;
        $cart->product_sku_id=$skuId;
        $cart->save();
        return [];
    }
}
