<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CartsController extends Controller
{
    //购物车列表
    public function index(Request $request)
    {
        $cartItems=$request->user()->cartItems()->with('productSku.product')->paginate();

        return CartItemResource::collection($cartItems);
    }

    //添加购物车
    public function addCart(AddCartRequest $request)
    {
        $user=$request->user();

        //如果已经添加购物车了
        if($cart=$user->cartItems()->where('product_sku_id',$request->sku_id)->first()){
            $cart->update([
                'amount'=>$cart->amount+$request->amount
            ]);
        }else{
            $cart=new CartItem;
            $cart->amount=$request->amount;
            $cart->user()->associate($user);
            $cart->productSku()->associate($request->sku_id);
            $cart->save();
        }
        return response()->json('添加成功',200);
    }

    //删除购物车商品
    public function destroy($skuId,Request $request)
    {
        if(!is_array($skuId)){
            $skuId=[$skuId];
        }
        $request->user()->cartItems()->whereIn('product_sku_id',$skuId)->delete();

        return response(null,204);
    }

    //修改商品数量
    public function update($skuId,AddCartRequest $request)
    {
        $cart=$request->user()->cartItems()->where('product_sku_id',$skuId)->first();

        $cart->update([
            'amount'=>$request->amount
        ]);
        return new CartItemResource($cart);
    }
}
