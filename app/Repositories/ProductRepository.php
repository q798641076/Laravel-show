<?php
namespace App\Repositories;

use App\Models\Product;
class ProductRepository
{
    public function index($request)
    {
        $builder=Product::query()->OnSale();

        if($search=$request->search)
        {   //查询时要用到% %
            $like='%'.$search.'%';
            //查询商品的名称，描述和sku的名称，描述
            $builder->FormSelect($like);
        }

        //对提交的order进行排序
        if($order=$request->order)
        {
            //对提交上来的order进行正则
            //1：正则 2：完整字符串 3：$m[0]完整字符串 $m[1]匹配的字符串 $m[2]剩余的字符串
            //price_desc  $m[1]:price $m[2]:desc
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['price','sold_count','rating'])){
                    //进行排序
                    $builder->orderBy($m[1],$m[2]);
                }
            }
        }

        return $builder;
    }
}
