<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderReview;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApplyRefundRequest;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Requests\Api\SendReviewRequest;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Jobs\CloseOrder;
use App\Models\CartItem;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $orders=$request->user()->orders()->with(['orderItems.product','orderItems.product_sku'])->get();

        return OrderResource::collection($orders);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own',$order);

        $order->load(['orderItems.product','orderItems.product_sku','couponCode'])->get();

        return new OrderResource($order);
    }

    public function store(OrderRequest $request)
    {
        $address    =   UserAddress::findOrFail($request->address_id);

        $remark     =   $request->remark?:null;

        if($code=$request->coupon_code){

            $code=CouponCode::query()->where('code',$request->coupon_code)->first();
            if(!$code){
                throw new CouponCodeUnavailableException('优惠卷不存在');
            }
            $code->checkCouponCode($request->user());
        }

        //开启事务管理
        $order=\DB::transaction(function () use($request,$address,$remark,$code){
            //创建订单
            $order=new Order([
                'address'       =>[
                    'address'       =>$address->full_address,
                    'zip'           =>$address->zip,
                    'contact_name'  =>$address->contact_name,
                    'contact_phone' =>$address->contact_phone
                ],
                'remark'        =>$remark,
                'total_amount'  =>0
            ]);
            $order->user()->associate($request->user());
            $order->save();
            //创建订单下的商品
            $total_amount=0;
            $items=$request->items;
            foreach($items as $item){
                $productSku=ProductSku::findOrFail($item['sku_id']);
                $orderItem=$order->orderItems()->make([
                    'price'     =>$productSku->price,
                    'amount'    =>$item['amount'],
                ]);
                $orderItem->product_sku()->associate($productSku);
                $orderItem->product()->associate($productSku->product_id);
                $orderItem->save();
                //计算总金额
                $total_amount+=$productSku->price*$item['amount'];
                //减库存
                if($productSku->decreaseStock($item['amount'])<=0){
                    throw new InvalidRequestException('库存不足');
                }
            }
            //优惠卷
            if($code){
                //检查金额是否达标
                $code->checkCouponCode($request->user(),$total_amount);
                //增加使用量
                if($code->couponCount()<=0){
                    throw new CouponCodeUnavailableException('优惠卷被抢光了');
                }
                //计算总金额
                $total_amount=$code->getAdjustedPrice($total_amount);
                //绑定优惠卷给订单
                $order->couponCode()->associate($code);
            }

            $order->update([
                'total_amount'=>$total_amount
            ]);

            //删除购物车
            $skuIds=collect($items)->pluck('sku_id');

            $request->user()->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

            return $order;
        });

        //开启队列，超过十分钟后未付款，删除订单，返回库存
        $this->dispatch(new CloseOrder($order,config('app.order_ttl')));

        return response()->json(['msg'=>'创建成功'],200);
    }

    //评论页面
    public function review(Order $order,Request $request)
    {
        if(!$order->paid_at){
            return response()->json('该订单未支付',403);
        }
        // if($order->ship_status!=Order::SHIP_STATUS_RECEIVED){
        //     return response()->json('确认收货后才能评价',403);
        // }
        // if($order->refund_status===Order::REFUND_STATUS_SUCCESS){
        //     return response()->json('您已经退款了',403);
        // }

        return new OrderResource($order->load(['orderItems.product','orderItems.product_sku']));
    }

    //提交评价
    public function sendReview(SendReviewRequest $request,Order $order)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            return response()->json('该订单未支付',403);
        }
        if($order->reviewed){
            return response()->json('该订单已评价',403);
        }

        $reviews=$request->reviews;

        \DB::transaction(function () use($reviews,$order){

            foreach($reviews as $review){
                $orderItem=OrderItem::findOrFail($review['id']);

                $orderItem->update([
                    'rating'      =>$review['rating'],
                    'review'      =>$review['review'],
                    'reviewed_at' =>Carbon::now(),
                ]);
            }
            //触发商品的平均分
            event(new OrderReview($order));

            //修改订单为已评价
            $order->update(['reviewed'=>true]);
        });
        return new OrderResource($order->load(['orderItems.product','orderItems.product_sku']));
    }

    //确认收货
    public function received(Order $order)
    {
        $this->authorize('own',$order);

        if(!$order->paid_at){
            return response()->json('订单未付款',403);
        }
        if($order->ship_status!==Order::SHIP_STATUS_DELIVERED){
            return response()->json('检查订单物流状态',403);
        }
        $order->update(['ship_status'=>Order::SHIP_STATUS_RECEIVED]);

        return response()->json('收货成功',200);
    }

    //申请退款
    public function refund(Order $order , ApplyRefundRequest $request)
    {
        if(!$order->paid_at){
            return response()->json('订单未付款',403);
        }
        if($order->refund_status!==Order::REFUND_STATUS_PENDING){
            return response()->json('请勿重复操作',403);
        }
        $extra=$order->extra ?:[];

        $extra['reason']=$request->reason;

        $order->update([
            'refund_status'=>Order::REFUND_STATUS_APPLIED,
            'extra'        =>$extra
            ]);

        return response()->json('申请成功',200);
    }
}
