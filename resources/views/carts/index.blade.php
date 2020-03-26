@extends('layouts.app')

@section('title','我的购物车')

@section('content')
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                购物车
            </div>
            <div class="card-body">
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
                                @if(!$cart->productSku->product->on_sale) disabled @endif>
                            </td>
                            <td>
                                <div class="product-info">
                                    <div class="preview">
                                       <a href="{{route('products.show',$cart->productSku->product->id)}}">
                                           <img src="{{$cart->productSku->product->image_url}}" alt="">
                                        </a>
                                    </div>
                                    <div @if(!$cart->productSku->product->on_sale) class="not-on-sale" @endif>
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
                               <input type="text" class="form-control form-control-sm amount"
                               value="{{$cart->amount}}" @if(!$cart->productSku->product->on_sale) disabled @endif
                               data-amount={{$cart->amount}} data-total={{$cart->productSku->stock}}>
                                @if ($cart->productSku->stock<=3&&$cart->productSku->product->on_sale)
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

                <div class="text-center font-weight-bold" style="font-size:20px;color:#cbcbcb">
                    总价：￥<span class="priceTotal text-danger">0</span>
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


})
    </script>
@endsection
