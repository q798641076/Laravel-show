<div class="box box-info">
    <div class="box-header">
        <div class="box-title">
            订单流水号：{{$order->no}}
        </div>
        <div class="box-tools float-right" style="margin-right:10px">
            <a href="{{route('admin.orders.index')}}" class="btn btn-info btn-sm">
                <i class="fa fa-list"></i> 列表
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tr>
                <td>买家：</td>
                <td>{{$order->user->name}}</td>
                <td>支付时间：</td>
                <td>{{$order->paid_at->format('Y-m-d H:i:s')}}</td>
            </tr>
            <tr>
                <td>支付方式：</td>
                <td>{{$order->paid_method}}</td>
                <td>支付渠道订单：</td>
                <td>{{$order->paid_no}}</td>
            </tr>
            <tr>
                <td>收获地址</td>
                <td colspan="3">
                    {{$order->address['address']}}{{$order->address['zip']}}
                    {{$order->address['contact_name']}}{{$order->address['contact_phone']}}
                </td>
            </tr>
            <tr>
                <td rowspan="{{$order->orderItems->count()+1}}">商品列表</td>
                <td>商品名称</td>
                <td>单价</td>
                <td>数量</td>
            </tr>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{$item->product->title}} <span style="color:#bcbcbc;font-size:15px">{{$item->product_sku->title}}</span></td>
                    <td class="text-danger">￥{{$item->product_sku->price}}</td>
                    <td>{{$item->amount}}</td>
                </tr>
            @endforeach
            <tr>
                <td>订单总金额：</td>
                <td class="text-danger">￥{{$order->total_amount}}</td>
                <td>发货状态：</td>
                <td>{{\App\Models\Order::$shipStatusMap[$order->ship_status]}}</td>
            </tr>
            <tr>
                {{-- 发货 --}}
                @if ($order->ship_status==\App\Models\Order::SHIP_STATUS_PENDING)
                 @if ($order->refund_status!==\App\Models\Order::REFUND_STATUS_SUCCESS)
                    <td colspan="4">
                        {!! Form::open(['route'=>['admin.orders.ship',$order->id],'method'=>'POST','class'=>'form-inline']) !!}
                        <div class="form-group @if($errors->has('express_company')) has-error @endif">
                            {!! Form::label('express_company', '物流公司', ['class'=>'control-label']) !!}
                            {!! Form::text('express_company', null, ['class'=>'form-control','placeholder'=>'物流公司','id'=>'express_company']) !!}
                        </div>
                        <div class="form-group @if($errors->has('express_no')) has-error @endif">
                            {!! Form::label('express_no', '物流单号', ['class'=>'control-label']) !!}
                            {!! Form::text('express_no', null, ['class'=>'form-control','placeholder'=>'物流单号','id'=>'express_no']) !!}
                        </div>
                        <button class="btn btn-success btn-sm" type="submit">发货</button>
                        {!! Form::close() !!}
                    </td>
                 @endif

                @else
                    <td>物流公司：</td>
                    <td>{{$order->ship_data['express_company']}}</td>
                    <td>物流单号：</td>
                    <td>{{$order->ship_data['express_no']}}</td>
                @endif
            </tr>
            {{-- 退款操作 --}}
            @if ($order->refund_status===\App\Models\Order::REFUND_STATUS_APPLIED)
            <tr>
                <td>退款状态</td>
                <td colspan="2">{{\App\Models\Order::$refundStatusMap[$order->refund_status]}}：{{$order->extra['reason']}}</td>
                <td>
                    @if ($order->refund_status===\App\Models\Order::REFUND_STATUS_APPLIED)
                        <button class="btn btn-sm btn-success btn-agree">同意退款</button>
                        <button class="btn btn-sm btn-danger btn-refusal">拒绝退款</button>
                    @endif
                </td>
            </tr>
            @endif

        </table>
    </div>
</div>

<script>
    $(document).ready(function(){
        //Laravel-Admin 使用的 SweetAlert 版本与我们在前台使用的版本不一样，因此参数也不太一样
        $('.btn-refusal').click(function(){
            swal({
                title:'拒绝退款理由',
                input:'text',
                showCancelButton:true,
                confirmButtonText:'确认',
                cancelButtonText:'取消',
                //显示一个在加载的进度条
                showLoaderOnConfirm:true,
                preConfirm:function(inputValue){
                    if(!inputValue){
                        swal('退款理由不呢为空','','error')
                        return false;
                    }
                    //Laravel-Admin 没有 axios，使用 jQuery 的 ajax 方法来请求
                    return $.ajax({
                        url:"{{route('admin.orders.refund',$order->id)}}",
                        type:"POST",
                        data: JSON.stringify({ // 将请求变成 JSON 字符串
                            agree:false,
                            reason:inputValue,
                            // 带上 CSRF Token
                            // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
                            _token:LA.token,
                        }),
                        contentType:'application/json' // 请求的数据格式为 JSON
                    });
                },
                //是否允许点击对话框外部来关闭对话框。
                allowOutsideClick:false
            }).then(function(ret){
                //如果点了取消就返回
                if(ret.dismiss=='cancel'){
                    return ;
                }
                swal('操作成功','','success')
                .then(function(){
                    location.reload()
                })
            })
        })


        $('.btn-agree').click(function(){
            swal({
                title:'确认退款吗',
                showCancelButton:true,
                cancelButtonText:'不确定',
                confirmButtonText:'确定退款',
                showLoaderOnConfirm:true,
                allowOutsideClick:false,
                //点击确认后请求体
                preConfirm:function(){
                return $.ajax({
                    url:"{{route('admin.orders.refund',$order->id)}}",
                    method:'POST',
                    // 带上 CSRF Token
                    // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
                    data:JSON.stringify({
                        agree: true,
                        _token:LA.token
                        }),
                    contentType:'application/json'
                })
                }
            }).then(function(ret){
                if(ret.dismiss=='cancel'){
                    return ;
                }

                swal('退款成功！','','success')
                    .then(function(){
                        location.reload();
                });
            })
        })
    })
</script>
