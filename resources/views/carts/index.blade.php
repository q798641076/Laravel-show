@extends('layouts.app')

@section('title','我的购物车')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                购物车
            </div>
            <div class="card-body">
                {{-- 购物车列表 --}}
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>商品信息</th>
                            <th>商品价格</th>
                            <th>数量</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $cart)
                        <tr data-id={{$cart->productSku->id}} class="cartItem">
                            <td>
                                <input type="checkbox" name="select"
                                @if(!$cart->productSku->product->on_sale|| $cart->productSku->stock==0) disabled @endif>
                            </td>
                            <td>
                                <div class="product-info">
                                    <div class="preview">
                                       <a href="{{route('products.show',$cart->productSku->product->id)}}">
                                           <img src="{{$cart->productSku->product->image_url}}" alt="">
                                        </a>
                                    </div>
                                    <div @if(!$cart->productSku->product->on_sale || $cart->productSku->stock==0) class="not-on-sale" @endif>
                                        <span class="product-title">
                                            <a href="{{route('products.show',$cart->productSku->product->id)}}">
                                                {{$cart->productSku->product->title}}
                                            </a>
                                        </span>
                                        <span class="sku-title">
                                            {{$cart->productSku->title}}
                                        </span>
                                        @if (!$cart->productSku->product->on_sale)
                                            <span class="warning">该商品已经下架了</span>
                                        @elseif($cart->productSku->stock==0)
                                            <span class="warning">该商品已经买完了</span>
                                        @endif
                                    </div>
                                </div>

                            </td>
                            <td>
                                <div class="price"><b>￥</b>
                                    <span class="sku-price" data-price={{$cart->productSku->price}}>{{$cart->productSku->price}}
                                    </span>
                                </div>
                            </td>
                            <td>
                               <input type="text" class="form-control form-control-sm amount" name='amount'
                               value="{{$cart->amount}}" @if(!$cart->productSku->product->on_sale) disabled @endif
                               data-amount={{$cart->amount}} data-total={{$cart->productSku->stock}}>
                                @if ($cart->productSku->stock<=3&&!$cart->productSku->stock==0&&$cart->productSku->product->on_sale)
                                    <div class="remain">
                                        剩下<span class="text-danger">{{$cart->productSku->stock}}</span>件，欲购从速
                                    </div>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-danger remove" >移除</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- 商品总价 --}}
                <div class="text-center font-weight-bold mb-3" style="font-size:20px;color:#cbcbcb">
                    总价：￥<span class="priceTotal text-danger">0</span>
                </div>

                {{-- 提交订单 --}}
                <div class="row col-md-10 offset-md-1">
                    {!! Form::open(['class'=>'form-horizontal','role'=>'form','id'=>'form-order']) !!}
                        {{-- 用户信息 --}}
                        <div class="form-group row" id="order-address">
                            <label for="form-address" class="col-form-label col-md-3 text-right">请选择信息</label>
                            <div class="col-md-9 ">
                                <select name="address"  class="form-control">
                                @foreach ($addresses as $address)
                                    <option value="{{$address->id}}">
                                        {{$address->full_address}}{{$address->contact_name}}{{$address->contact_phone}}
                                    </option>
                                @endforeach
                            </select>
                            </div>

                        </div>
                        {{-- 备注 --}}
                        <div class="form-group row" id="order-remark">
                            <label  class="col-form-label col-md-3 text-right">备注</label>
                            <div class="col-md-9">
                                {!! Form::textarea('remark',null, ['class'=>'form-control','rows'=>3,'placeholder'=>'您的备注']) !!}
                            </div>
                        </div>

                         {{-- 优惠码 --}}
                        <div class="form-group row">
                            <label for="coupon_code" class="col-form-label col-sm-3 text-right">优惠码</label>
                            <div class="col-sm-5">
                                <input type="text" name='coupon_code' id='coupon_code' placeholder="请输入优惠码" class="form-control ">
                                <span class="coupon-info" style="font-size:13px;color:#cbcbcb"></span>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-success " id="coupon-check">检查</button>
                                <button type="button" class="btn btn-danger" id="coupon-cancel" style="display:none">取消</button>
                            </div>

                        </div>
                    {!! Form::close() !!}

                    <div class="form-group col-md-6 text-right">
                        <button class="btn btn-success" id="btn-create-order" >去付款</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptAfterJs')
    <script>
        var priceTotal=0,total=0;
        $(document).ready(function()
        {
            //删除购物车商品
            $('.remove').click(function(){
            // closest() 方法可以获取到匹配选择器的第一个祖先元素，在这里就是当前点击的 移除 按钮之上的 <tr> 标签
                var id= $(this).closest('tr').data('id');

                swal({
                    title:'确定要删除吗',
                    icon:'warning',
                    buttons:['取消','确定'],
                    dangerMode:true
                }).then(function(willDelete){
                    if(!willDelete){
                         return ;
                    }

                axios.delete("/cart/"+id)
                    .then(function(){
                    swal({
                        title:'删除成功',
                        icon:'success'
                    }).then(function(){
                        location.reload()
                    })
                },function(error){
                    if(error.response.status===401){
                        swal({
                        title:'你没权访问',
                        icon:'danger'
                    })
                    }
                })
            })
        })

            // $('#select-all').change(function(){
            //     // 获取单选框的选中状态
            //     // prop() 方法可以知道标签中是否包含某个属性，当单选框被勾选时，对应的标签就会新增一个 checked 的属性
            //     var checked = $(this).prop('checked');
            //     // 获取所有 name=select 并且不带有 disabled 属性的勾选框
            //     // 对于已经下架的商品我们不希望对应的勾选框会被选中，因此我们需要加上 :not([disabled]) 这个条件
            //     $('input[name=select][type=checkbox]:not([disabled])').each(function() {
            //         // 将其勾选状态设为与目标单选框一致
            //         $(this).prop('checked', checked);
            //     });

            // });

                //动态获取价格
            $('input[name=select][type=checkbox]').change(function(){

                    price=$(this).closest('tr').find('.sku-price').data('price');
                    amount=$(this).closest('tr').find('.amount').data('amount');
                    total=price*amount;
                    if($(this).is(':checked')){
                        priceTotal+=total;
                    }else{
                        if(priceTotal<=0){
                            return ;
                        }
                        priceTotal-=total;
                    }
                    $('.priceTotal').html(priceTotal);
        })

        //修改数量
            $('.amount').change(function(){

                $(this).blur(function(){
                id=$(this).closest('tr').data('id');

                if($(this).val()>$(this).data('total')){
                    swal({
                        title:'库存不足',
                        icon:'error',
                    })
                    return ;
                }
                axios.put('/cart/'+id,{
                    'amount':$(this).val()
                })
                     .then(function(){
                        swal({
                        title:'修改成功',
                        icon:'success',
                    }).then(function(){
                        location.reload()
                    })
                },function(error){
                    swal({
                        title:'修改失败',
                        icon:'error',
                    })
                })
            })
        })

            //提交订单
            $('#btn-create-order').click(function(){

                    var rad={
                        address_id:$('#order-address').find('select[name=address]').val(),
                        remark:$('#order-remark').find('textarea[name=remark]').val(),
                        items:[]
                    }

                    $('table tr[data-id]').each(function(){
                        //如果没有被选中则return
                        $input=$(this).find('input[name=select][type=checkbox]');

                        if($input.prop('disabled') || !$input.prop('checked')){
                            return;
                        }
                        $amount=$(this).find('input[name=amount]');

                        if($amount.val()==0 || isNaN($amount.val())){
                            swal('检查数量','','error')
                            return ;
                        }

                        rad.items.push({
                            amount:$amount.val(),
                            sku_id:$(this).data('id')
                        })
                    })


                //提交
                axios.post("{{route('orders.store')}}",rad)
                     .then(function(data){
                         swal('订单提交成功','','success').then(function(){
                             location.href='/orders/'+data.data.id;
                         })
                     },function(error){
                        if(error.response.status==422){
                            //422说明后台验证出了问题
                           $.each(error.response.data.errors,function(index,value){
                            html=value[0]
                           })
                           swal({title:html,icon:'error'})
                        }else{
                            swal('系统错误','','error')
                        }

                     })
            })

            $('#coupon-check').click(function(){
                $code=$('input[name=coupon_code]').val();
                if(!$code){
                    swal('请输入优惠码','','error');
                    return;
                }
                axios.get('/coupons/'+$code)
                    .then(function(response){
                        //获取优惠卷信息
                       $('.coupon-info').html(response.data.description);
                       //禁用文本框
                       $('input[name=coupon_code]').prop('disabled',true);
                       //开启取消按钮
                       $('#coupon-cancel').show();
                       //隐藏检查按钮
                       $('#coupon-check').hide();
                    },function(error){
                        if(error.response.status===403){
                            swal(error.response.data.msg,'','error');
                        } else if(error.response.status===404) {
                            swal('优惠码不存在','','error');
                        } else{
                            swal('服务器错误','','error');
                        }
                    })
            })

            $('#coupon-cancel').click(function(){
                $('input[name=coupon_code]').prop('disabled',false);
                $('.coupon-info').html('');
                //隐藏取消按钮
                $('#coupon-cancel').hide();
                //开启检查按钮
                $('#coupon-check').show();
            })

})
    </script>
@endsection
