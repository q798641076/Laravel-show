@extends('layouts.app')

@section('title','订单详情')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>订单详情</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>商品信息</th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>小计</th>
                        </tr>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td class="product-info">
                                    <div class="preview">
                                        <a href="{{route('products.show',$item->product_id)}}">
                                            <img src="{{$item->product->image_url}}" alt="">
                                        </a>
                                    </div>
                                    <div>
                                        <a href="{{route('products.show',$item->product_id)}}">
                                            <span class="product-title">{{$item->product->title}}</span>
                                        </a>
                                        <span class="sku-title">{{$item->product_sku->title}}</span>
                                    </div>
                                </td>
                                <td>￥{{number_format($item->price,2)}}</td>
                                <td>{{$item->amount}}</td>
                                <td>￥{{number_format($item->price*$item->amount,2)}}</td>
                            </tr>
                        @endforeach
                            <tr><td colspan="4"></td></tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line">
                                <div class="line-label">收货地址：</div>
                                <div class="line-value">{{join(' ',$order->address)}}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单备注：</div>
                                <div class="line-value">{{$order->remark}}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">订单编号：</div>
                                <div class="line-value">{{$order->no}}</div>
                            </div>
                        </div>
                        <div class="order-summary text-right">
                            <div class="total-amount">
                                总价：
                                <div class="value">￥{{number_format($order->total_amount,2)}}</div>
                            </div>
                            <div class="total-amount">
                                订单状态：
                                <div class="value">
                                    @if ($order->paid)
                                    @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                    已支付
                                    @else
                                    {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                    @endif
                                    @elseif($order->closed)
                                        订单已关闭
                                    @else
                                        未支付
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
