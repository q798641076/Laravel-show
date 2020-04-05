<?php
namespace App\Services;

use App\Events\OrderReview;
use App\Exceptions\CouponCodeUnavailableException;
use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Models\UserAddress;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\OrderItem;

class OrderServices
{
    public function store($user, $address, $remark, $items, $couponCode)
    {
        //如果优惠卷存在
        if($couponCode){
            //现在还不需要进行订单金额的判断
            $couponCode->checkCouponCode($user);
        }

         //开启事务回滚系统
         $order=\DB::transaction(function () use($user,$address,$remark,$items,$couponCode){
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
                // 创建一个 OrderItem 并直接与当前订单关联 但不保存到数据库
                //等同于 $orderItem=new OrderItem(), $orderItem->order()->associate($order)
                $orderItem=$order->orderItems()->make([
                    'amount'=>$item['amount'],
                    'price'=>$sku->price,
                ]);
                //关联
                $orderItem->product_sku()->associate($sku);
                $orderItem->product()->associate($sku->product_id);
                $orderItem->save();
                //计算总价
                $total_amount+=$sku->price*$item['amount'];
                //减库存：
                if($sku->decreaseStock($item['amount'])<=0){
                    throw new InvalidRequestException('库存不足');
                }
            }

            if($couponCode){
                //进行订单金额判断
                $couponCode->checkCouponCode($total_amount);
                //折扣后的金额
                $total_amount=$couponCode->getAdjustedPrice($total_amount);
                //使用量进行验证
                if($couponCode->couponCount()<=0){

                    throw new CouponCodeUnavailableException('优惠卷被抢光了');
                }
                //最后订单关联优惠卷
                $order->couponCode()->associate($couponCode);
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

    public function sendReview($order,$reviews)
    {
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付');
        }
        if($order->reviewed){
            throw new InvalidRequestException('订单已评论');
        }

        \DB::transaction(function () use($reviews,$order){
            //包含了整个订单下面的订单项 reviews

            //循环每个商品下面的评价
            foreach($reviews as $review){
                $orderItem=OrderItem::findOrFail($review['id']);
                $orderItem->update([
                    'review'=>$review['review'],
                    'rating'=>$review['rating'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            //添加评论后，修改商品的评论数和评论平均分的事件
            event(new OrderReview($order));
            //将order修改为已评论
            $orderItem->order()->update([
                'reviewed'=>true
            ]);
        });
    }
}
