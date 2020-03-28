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
use Carbon\Carbon;

class OrdersController extends Controller
{
    //订单页面展示
    public function index(Request $request)
    {
        $orders=$request->user()
                        ->orders()
                        ->with(['orderItems.product','orderItems.product_sku'])
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
    public function store(OrderRequest $request)
    {
        $user=$request->user();

        //开启事务回滚系统
        $order=\DB::transaction(function () use($user,$request){
            //找到地址数据表，将地址数据表的内容以json的形式填入我们订单下面的地址
            $address=UserAddress::findOrFail($request->address_id);
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            //新建一个订单项
            $order=new Order([
                'address'=>[
                    'address'=>$address->full_address,
                    'zip'=>$address->zip,
                    'contact_name'=>$address->contact_name,
                    'contact_phone'=>$address->contact_phone
                ],
                'remark'=>$request->remark,
                'total_amount'=>0
            ]);
            //关联用户
            $order->user()->associate($user);
            $order->save();

            //新建一个或多个订单下面的商品
            $total_amount=0;
            $items=$request->items;
            foreach($items as $item){
                $sku=ProductSku::findOrFail($item['sku_id']);
                //用订单关联订单商品来创建
                $orderItem=$order->orderItems()->make([
                    'amount'=>$item['amount'],
                    'price'=>$sku->price,
                ]);
                //关联
                $orderItem->product_sku()->associate($sku);
                $orderItem->product()->associate($sku->product_id);
                $orderItem->save();
                $total_amount+=$sku->price*$item['amount'];
                //减库存：
                if($sku->decreaseStock($item['amount'])<=0){
                    throw new InvalidRequestException('库存不足');
                }
            }
            $order->update(['total_amount'=>$total_amount]);

            //删除购物车
            $skuId=collect($items)->pluck('sku_id');

            $user->cartItems()->whereIn('product_sku_id',$skuId)->delete();

            return $order;

        });
            //接下来我们需要在创建订单之后触发这个任务：
            $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

            return $order;
    }
}
