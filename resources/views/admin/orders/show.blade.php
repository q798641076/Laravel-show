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
                @else
                    <td>物流公司：</td>
                    <td>{{$order->ship_data['express_company']}}</td>
                    <td>物流单号：</td>
                    <td>{{$order->ship_data['express_no']}}</td>
                @endif
            </td>
        </table>
    </div>
</div>
