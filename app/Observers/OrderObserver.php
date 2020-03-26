<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function creating(Order $order)
    {
        //在写入数据前触发
        if(!$order->no){
            //调用findAvailableNo生成随机订单流水号
            $order->no=$order->findAvailableNo();
            //如果生成失败
            if(!$order->no){
                return ;
            }
        }
    }
}
