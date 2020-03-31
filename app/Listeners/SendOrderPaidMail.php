<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Notifications\OrderPaidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderPaidMail implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order=$event->getOrder();

        //调用User中的notify方法来发送邮件
        $order->user->notify(new OrderPaidNotification($order));
    }
}
