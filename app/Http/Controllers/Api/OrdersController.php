<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\CloseOrder;
use App\Models\CartItem;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSku;
use App\Models\UserAddress;
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
}
