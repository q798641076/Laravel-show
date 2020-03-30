<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        //从事件取出对应的订单
        $order=$event->getOrder();
        //预加载订单下面的订单项和对应的商品
        $order->with('orderItems.product');
        //循环遍历取出商品
        foreach($order->orderItems as $item){
            $product=$item->product;
            //获取订单列表下的所有已支付的对应的商品，amount总和
            $sold_count=OrderItem::where('product_id',$product->id)
                                 ->whereHas('order',function($query){
                                      $query->whereNotNull('paid_at'); //关联的订单是已支付的
                                 })->sum('amount');
            //更改销量
            $product->sold_count=$sold_count;
            $product->save();
            //别忘了在 EventServiceProvider 中将事件和监听器关联起来：
        }
    }
}
