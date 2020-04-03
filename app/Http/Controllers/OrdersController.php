<?php

namespace App\Http\Controllers;

use App\Events\OrderReview;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\AppliedRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Jobs\CloseOrder;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Services\CartServices;
use App\Services\OrderServices;
use Carbon\Carbon;

class OrdersController extends Controller
{
    //订单页面展示
    public function index(Request $request)
    {
        $orders=$request->user()
                        ->orders()
                        ->with(['orderItems.product','orderItems.product_sku'])
                        ->orderBy('created_at','desc')
                        ->paginate();

        return view('orders.index',compact('orders'));
    }

    //订单详情
    public function show(Order $order , Request $request)
    {
        $this->authorize('own',$order);
        $order=$order->load(['orderItems.product','orderItems.product_sku']);
        return view('orders.show',compact('order'));
    }

    //订单提交逻辑
    public function store(OrderRequest $request,OrderServices $orderServices)
    {
        $address=UserAddress::findOrFail($request->address_id);

        $order=$orderServices->store($request->user(),$address,$request->remark,$request->items);

        return $order;
    }

    //确认收货
    public function received(Order $order)
    {
        $this->authorize('own',$order);
        if(!$order->paid_at){
            throw new InvalidRequestException('未付款');
        }
        if($order->ship_status!==Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('未发货或者已经确认收货');
        }
        $order->update([
            'ship_status'=>Order::SHIP_STATUS_RECEIVED
        ]);
        return [];
    }

    //评价
    public function review(Order $order, Request $request)
    {
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付');
        }

        $this->authorize('own',$order);

        return view('orders.review',['order'=>$order->load(['orderItems.product','orderItems.product_sku'])]);
    }
    //发送评论
    public function sendReview(Order $order, SendReviewRequest $request,OrderServices $orderServices)
    {
        $this->authorize('own',$order);

        $orderServices->sendReview($order,$request->input('reviews'));

        return [];
    }

    //申请退款
    public function appliedRefund(AppliedRefundRequest $request,Order $order)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付');
        }
        //证明已经在申请过程中了
        if($order->refund_status!==Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('请勿重复申请');
        }
        //把退款理由存入订单的extra字段
        $extra=$order->extra ? :[];

        $extra['reason']=$request->reason;
        unset($extra['refusal_reason']);
        $order->update([
            'extra'=>$extra,

            'refund_status'=>Order::REFUND_STATUS_APPLIED
        ]);

        return [];
    }
}
