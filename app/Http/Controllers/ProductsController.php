<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Repositories\ProductRepository;

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
}
