<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use App\Notifications\OrderDeliveredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderDeliveredMail implements ShouldQueue
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
     * @param  OrderDelivered  $event
     * @return void
     */
    public function handle(OrderDelivered $event)
    {
        $order=$event->getOrder();
        //发送
        $order->user->notify(new OrderDeliveredNotification($order));
    }
}
