<?php

namespace App\Http\Controllers;

use App\Events\OrderReview;
use App\Exceptions\InvalidRequestException;
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
        $user=$request->user();
        $address=UserAddress::findOrFail($request->address_id);
        $remark=$request->remark;
        $items=$request->items;
        $order=$orderServices->store($user,$address,$remark,$items);
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
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            throw new InvalidRequestException('订单未支付');
        }
        if($order->reviewed){
            throw new InvalidRequestException('订单已评论');
        }

        \DB::transaction(function () use($request,$order){
            //包含了整个订单下面的订单项
            $reviews=$request->input('reviews');
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

        return [];
    }
}
