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
                            @if($order->ship_status!==\App\Models\Order::SHIP_STATUS_PENDING)
                            <div class="line">
                                <div class="line-label">物流状态：</div>
                                <div class="line-value">{{\App\Models\Order::$shipStatusMap[$order->ship_status]}}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">物流信息：</div>
                                <div class="line-value">{{$order->ship_data['express_company']}} {{$order->ship_data['express_no']}}</div>
                            </div>
                            @endif
                            @if ($order->paid_at && $order->refund_status!==\App\Models\Order::REFUND_STATUS_PENDING)
                            <div class="line">
                                <div class="line-label">退款理由：</div>
                                <div class="line-value">{{$order->extra['reason']}}</div>
                            </div>
                            @endif
                        </div>
                        <div class="order-summary text-right">
                            <div class="total-amount">
                                总价：
                                <div class="value">￥{{number_format($order->total_amount,2)}}</div>
                            </div>
                            <div class="total-amount">
                                订单状态：
                                <div class="value">
                                    @if ($order->paid_at)
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
                            @if ($order->extra['refusal_reason'])
                                <div class="total-amount">
                                    拒绝退款：
                                    <div class="value">
                                        {{$order->extra['refusal_reason']}}
                                    </div>
                                </div>
                            @endif
                            <div class="received mt-2">
                                    @if ($order->ship_status===\App\Models\Order::SHIP_STATUS_DELIVERED)
                                        <button class="btn btn-success received-btn" >确认收货</button>
                                    @endif
                                    @if ($order->paid_at&&$order->refund_status===\App\Models\Order::REFUND_STATUS_PENDING)
                                        <button class="btn btn-danger refund-btn" >申请退款</button>
                                    @endif
                            </div>
                            @if (!$order->paid_at)
                                <div class="payment">
                                    支付方式：
                                    <div class="value">
                                        <a href="{{route('payment.alipay',$order->id)}}"  >
                                            <img src="{{asset('upload\images\alipay.jpg')}}" alt="" width="35px">
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptAfterJs')
    <script>
        $(document).ready(function(){
            $('.received-btn').click(function(){
                swal({
                    title:'确认收货吗？',
                    icon:'warning',
                    buttons:['取消','确定收货'],
                    dangerMode:true
                }).then(function(rel){
                    if(!rel){
                        return;
                    }
                    axios.post("{{route('orders.received',$order->id)}}")
                         .then(function(){
                             location.reload()
                    })
                })
            });

            $('.refund-btn').click(function(){
                swal({text:'请输入退款理由',content:'input'})
                .then(function(input){
                    if(!input){
                        swal({title:'退款理由不能为空',icon:'error'})
                        return ;
                    }
                    axios.post("/orders/{{$order->id}}/refund",{reason:input})
                        .then(function(){
                            swal('申请成功','','success')
                        },function(error){
                            swal('系统错误','','error')
                    })
                })
            })
        })
    </script>
@endsection
