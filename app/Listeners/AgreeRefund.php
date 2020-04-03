<?php

namespace App\Listeners;

use App\Events\OrderRefund;
use App\Notifications\OrderRefunNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AgreeRefund implements ShouldQueue
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
     * @param  OrderRefund  $event
     * @return void
     */
    public function handle(OrderRefund $event)
    {
        $order=$event->getOrder();
        $order->user->notify(new OrderRefunNotification($order));
    }
}
