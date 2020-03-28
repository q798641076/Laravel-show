@extends('layouts.app')

@section('title','订单列表')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    订单列表
                </div>
                <div class="card-body">
                    @if (!count($orders))
                     <p class="text-gray text-center">暂无订单/(ㄒoㄒ)/~~</p>
                    @else

                    <ul class="list-group">
                        @foreach ($orders as $order)
                            <li class="list-group-item">
                                <div class="card">
                                    <div class="card-header">
                                        订单号：{{$order->no}}
                                        <span class="float-right">{{$order->created_at->format('Y-m-d H:i:s')}}</span>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>商品信息</th>
                                                    <th>单价</th>
                                                    <th>数量</th>
                                                    <th>订单总价</th>
                                                    <th>状态</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $index=>$item)
                                                    <tr>
                                                        <td class="product-info">
                                                            <div class="preview">
                                                                <a href="{{route('products.show',$item->product->id)}}">
                                                                   <img src="{{$item->product->image_url}}" alt="">
                                                                </a>
                                                            </div>
                                                            <div>
                                                            <span class="product-title">
                                                                <a href="{{route('products.show',$item->product->id)}}">
                                                                    {{$item->product->title}}
                                                                </a>
                                                            </span>
                                                            <span class="sku-title">
                                                                {{$item->product_sku->title}}
                                                            </span>
                                                            </div>

                                                        </td>
                                                        <td class="price">￥{{$item->price}}</td>
                                                        <td class="amount">{{$item->amount}}</td>
                                                        @if ($index===0)
                                                            <td rowspan="{{$order->orderItems->count()}}" class="text-center total-amount">
                                                                ￥{{$order->total_amount}}
                                                            </td>
                                                            <td rowspan="{{$order->orderItems->count()}}" class="text-center">
                                                                @if ($order->paid_at)
                                                                    @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                                                    已支付
                                                                    @else
                                                                    {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                                                    @endif
                                                                @elseif($order->closed)
                                                                    订单已关闭
                                                                @else
                                                                    未付款<br>
                                                                    请在{{$order->created_at->addSeconds(config('app.order_ttl'))->format('H:i')}}
                                                                    内支付<br>不然订单将关闭
                                                                @endif
                                                            </td>
                                                            <td class="text-center" rowspan="{{$order->orderItems->count()}}">
                                                                <a href="" class="btn btn-primary">查看订单</a>
                                                            </td>
                                                        @endif

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
