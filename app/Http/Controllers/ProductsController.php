<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Console\Presets\React;

class ProductsController extends Controller
{
    protected $pro;

    public function __construct(ProductRepository $pro)
    {
        $this->pro=$pro;
    }

    public function index(Request $request)
    {

        //构建一个查询构造器 对其进行搜索，排序
        $builder=$this->pro->index($request);

        $products=$builder->paginate(16);
        //将用户查询的值返回
        $filters=[
            'search'=>$request->search,
            'order'=>$request->order
        ];
        return view('products.index',compact('products','filters'));
    }

    public function show(Product $product, Request $request)
    {
        //判断是否在售
        if(!$product->on_sale){
            throw new InvalidRequestException('商品未上架');
        }

        //控制器把收藏状态注入模板中
        $favorite=false;

        if($user=$request->user()){

            $favorite=boolval($user->favoriteProducts()->find($product->id));

        }

        return view('products.show',compact('product','favorite'));
    }

    public function favorite(Product $product,Request $request)
    {
        $user=$request->user();

        //如果已经收藏
        if($user->favoriteProducts()->find($product->id))
        {
           return [];
        }

        //用attach将user和product关联起来
        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disFavorite(Product $product, Request $request)
    {
        $user=$request->user();

        $user->favoriteProducts()->detach($product);

        return [];
    }

    //收藏页面
    public function favoriteShow(Request $request)
    {
        $user=$request->user();

        $products=$user->favoriteProducts()->paginate(16);

        return view('products.favorite',compact('products'));
    }
}
