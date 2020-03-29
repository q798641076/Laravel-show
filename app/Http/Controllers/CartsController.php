<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductSku;
use App\Services\CartServices;

class CartsController extends Controller
{

    public $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices=$cartServices;
    }

    //购物车列表
    public function index(Request $request)
    {
        //productSku.product ：laravel支持用.的方式来获取多层关联的数据 预加载
        $cartItems=$this->cartServices->get();
        //注入我们的地址到购物车列表
        $addresses=$request->user()->addresses()->orderBy('last_used_at')->get();

        return view('carts.index',compact('cartItems','addresses'));
    }

    //添加购物车
    public function addCart(AddCartRequest $request)
    {
        $this->cartServices->add($request->sku_id,$request->amount);

        return [];
    }

    public function destroy($sku , Request $request)
    {

        //这样就不用验证是否是自己删除
        $this->cartServices->remove($sku);

        return [];
    }

    public function update(ProductSku $sku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id',$sku->id)->update([
            'amount'=>$request->amount
        ]);

        return [];
    }

}
