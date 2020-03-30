<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $order;

    public function __construct(Order $order)
    {
        $this->order=$order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    //事件本身不需要有逻辑，只需要包含相关的信息即可，在我们这个场景里就只需要一个订单对象。

    //接下来我们在支付成功的服务器端回调里触发这个事件：
    public function getOrder()
    {
        return $this->order;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
