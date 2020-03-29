<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Models\UserAddress;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;

class OrderServices
{
    public function store($user, $address, $remark, $items)
    {
         //开启事务回滚系统
         $order=\DB::transaction(function () use($user,$address,$remark,$items){
            //找到地址数据表，将地址数据表的内容以json的形式填入我们订单下面的地址
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
                'remark'=>$remark,
                'total_amount'=>0
            ]);
            //关联用户
            $order->user()->associate($user);
            $order->save();

            //新建一个或多个订单下面的商品
            $total_amount=0;
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
            //CartService 的调用方式改为了通过 app() 函数创建，
            //因为这个 store() 方法是我们手动调用的，无法通过 Laravel 容器的自动解析来注入。
            //在我们代码里调用封装的库绝对不可以用new来初始化
            app(CartServices::class)->remove($skuId);

            return $order;

        });
            //在service中使用队列不用$this->dispatch() 直接用dispatch（），控制器中才可以用$this
            dispatch(new CloseOrder($order,config('app.order_ttl')));

            return $order;
    }
}
