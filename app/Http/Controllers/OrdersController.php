<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Services\CartServices;
use App\Services\OrderServices;
use Carbon\Carbon;

class OrdersController extends Controller
{
    //订单页面展示
    public function index(Request $request)
    {
        $orders=$request->user()
                        ->orders()
                        ->with(['orderItems.product','orderItems.product_sku'])
                        ->orderBy('created_at','desc')
                        ->paginate();

        return view('orders.index',compact('orders'));
    }

    //订单详情
    public function show(Order $order , Request $request)
    {
        $order=$request->user()
                       ->orders()
                       ->with(['orderItems.product','orderItems.product_sku'])
                       ->findOrFail($order->id);
        return view('orders.show',compact('order'));
    }

    //订单提交逻辑
    public function store(OrderRequest $request,OrderServices $orderServices)
    {
        $user=$request->user();
        $address=UserAddress::findOrFail($request->address_id);
        $remark=$request->remark;
        $items=$request->items;
        $order=$orderServices->store($user,$address,$remark,$items);
        return $order;
    }
}
