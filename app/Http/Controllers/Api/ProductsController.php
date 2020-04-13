<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $builder=QueryBuilder::for(Product::class)->onSale();
        //搜索
        if($search=$request->search){

            $like='%'.$search.'%';

            $builder->formSelect($like);

        }
        //排序
        if($order=$request->order){
            preg_match('/^(.+)_(asc|desc)$/',$order,$m);
            if(in_array($m[1],['price','rating','sold_count'])){
                $builder->orderBy($m[1],$m[2]);
            }
        }

        $products=$builder->paginate();

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        if(!$product->on_sale){
            throw new InvalidRequestException('商品已经下架了');
        }

        //预加载，避免N+1;
        $product=QueryBuilder::for(Product::class)
                ->allowedIncludes('skus')
                ->findOrFail($product->id);

        return new ProductResource($product);
    }

    public function favorite(Product $product,Request $request)
    {
        $user=$request->user();

        if($user->favoriteProducts()->find($product->id)){
            return response('该商品已经在收藏夹',403);
        }
        //用attach将user和product关联起来
        $user->favoriteProducts()->attach($product);

        return response('收藏成功',200);
    }

    public function undoFavorite(Product $product,Request $request)
    {
        $user=$request->user();

        $user->favoriteProducts()->detach($product);

        return response(null,204);
    }
}
