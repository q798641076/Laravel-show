<?php

namespace App\Jobs;

use App\Models\CouponCode;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $order;

    public function __construct(Order $order,$delay)
    {
        $this->order=$order;
        //设置延迟时间，多少秒后执行handle()
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //开启事务管理系统
        \DB::transaction(function () {

        //如果订单被支付，就返回
        if($this->order->paid_at){
            return ;
        }
        //否则取消订单，恢复库存
        $this->order->update(['closed'=>true]);

        foreach($this->order->orderItems as $item){
            $item->product_sku->addStock($item->amount);
        }
        //恢复优惠卷库存
        $this->order->couponCode->CouponCount(false);

        });

    }
}
