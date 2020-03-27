<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Carbon\Carbon;

class OrdersController extends Controller
{
    //订单提交逻辑
    // public function store(OrderRequest $request)
    // {
        // $user=$request->user();
        // //开始事务回滚系统
        // $order=\DB::transaction(function () use($user,$request){

        //     $address=UserAddress::findOrFail($request->address_id);
        //     //更改最后的使用时间
        //     $address->update(['last_used_at'=>Carbon::now()]);
        //     //新建订单
        //     $order=new Order([
        //         //地址接收一个json格式
        //         'address'=>[
        //             'address'=>$address->full_address,
        //             'zip'=>$address->zip,
        //             'contact_name'=>$address->contact_name,
        //             'contact_phone'=>$address->contact_phone
        //         ],
        //         'remark'=>$request->remark,
        //         'total_amount'=>0
        //     ]);
        //     // 订单关联到当前用户
        //     $order->user()->associate($user);
        //     //存入数据库
        //     $order->save();

        //     //orderItem逻辑
        //     $items=$request->items;
        //     //重置total_amount
        //     $total_amount=0;
        //     //循环每一个订单列
        //     foreach($items as $item){
        //         $sku=ProductSku::findOrFail($item['sku_id']);
        //         //创建订单关联的订单项 要用make:
        //         $orderItem=$order->orderItems()->make([
        //             'price'=>$sku->price,
        //             'amount'=>$item['amount']
        //         ]);
        //         $orderItem->product()->associate($sku->product_id);
        //         $orderItem->product_sku()->associate($sku);
        //         $orderItem->save();
        //         $total_amount+=$sku->price*$item['amount'];
        //         //减库存
        //         if($sku->decreaseStock($item['amount'])<0){
        //             throw new InvalidRequestException('库存不足');
        //         }
        //     }
        //     //修改order订单总金额
        //     $order->update(['total_amount'=>$total_amount]);

        //     //成功以后删除购物车提交的订单 ['sku_id1','sku_id2','sku_id3']
        //     $skuIds=collect($items)->pluck('sku_id');
        //     //whereIn 第二个参数可以插入数组
        //     $user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

        //     return $order;
        // });

        //     return $order;

    // }

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

        return $order;
    }
}
