<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductSku;

class CartsController extends Controller
{
    //购物车列表
    public function index(Request $request)
    {
        //productSku.product ：laravel支持用.的方式来获取多层关联的数据 预加载
        $cartItems=$request->user()->cartItems()->with('productSku.product')->get();

        return view('carts.index',compact('cartItems'));
    }

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

    public function destroy(ProductSku $sku , Request $request)
    {
       
        //这样就不用验证是否是自己删除
        $request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();

        return [];
    }
}
