@extends('layouts.app')

@section('title','商品详情')

@section('content')
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                <div class="row goods-show">

                    <div class="col-md-5">
                        <img src="{{$product->image_url}}" alt="" class="image">
                    </div>

                    <div class="col-md-7 details">
                        <div class="title">{{$product->title}}</div>
                        <div class="price">价格<b>￥</b><span>{{$product->price}}</span></div>
                        <div class="row sold_and_review">
                            <div class="col-md-4 sold_count">累计销量<span>{{$product->sold_count}}</span></div>
                            <div class="col-md-4 review_count">累计评价<span>{{$product->review_count}}</span></div>
                            <div class="col-md-4 rating">评分
                                <span class="count" title="评分--{{$product->rating}}">
                                    {{ str_repeat('★',floor($product->rating)) }}{{ str_repeat('☆',floor(5-$product->rating)) }}
                                </span>
                            </div>
                        </div>

                        <div class="sku">
                            <label for="" class="">选择</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                @foreach ($product->skus as $sku)
                                    <button for="" class="btn sku-btn"
                                    title="{{$sku->description}}"
                                    data-price={{$sku->price}}
                                    data-stock={{$sku->stock}}
                                    data-toggle="tooltip"
                                    >
                                        <input type="radio"  name="sku_id" value="{{$sku->id}}">
                                        {{$sku->title}}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="goods-count">
                            <label>数量</label>
                            <input type="text" class="form-control form-control-sm amount" name="amount">
                            <span>件</span><span class="stock"></span>
                        </div>

                        <div class="btn-group anthor-btn">
                            @if ($favorite)
                            <button class="btn btn-danger disfavor-btn">取消收藏</button>
                            @else
                            <button class="btn btn-success favor-btn">❤收藏</button>
                            @endif
                            <button class="btn btn-primary cart-btn"><i class="fa fa-shopping-cart" aria-hidden="true"></i>加入购物车</button>
                        </div>
                    </div>
                </div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-description" role="tab" aria-controls="nav-description" aria-selected="true">商品详情</a>
                      <a class="nav-item nav-link" id="nav-review-tab" data-toggle="tab" href="#nav-review" role="tab" aria-controls="nav-profile" aria-selected="false">用户评价</a>
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">
                        {!!$product->description!!}
                    </div>
                    <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                        <table class="table table-border table-striped">
                            <tr>
                                <th>用户</th>
                                <th>商品</th>
                                <th>评分</th>
                                <th>评价</th>
                                <th>评价时间</th>
                            </tr>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>{{$review->order->user->name}}</td>
                                    <td>{{$review->product_sku->title}}</td>
                                    <td class="text-danger">{{str_repeat('★',$review->rating)}}{{str_repeat('☆',5-$review->rating)}}</td>
                                    <td>{{$review->review}}</td>
                                    <td>{{$review->reviewed_at}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                  </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptAfterJs')
<script>
    $(document).ready(function(){
        //boostrap提示工具
        $('[data-toggle=tooltip]').tooltip({trigger:'hover'});

        //价格和库存变动
        $('.sku-btn').click(function(){
            $('.details .price span').text($(this).data('price'));
            $('.details .goods-count .stock').text("库存:"+$(this).data('stock')+"件");
        })

        //收藏
        $('.favor-btn').click(function(){
            axios.post("{{route('products.favorite',$product->id)}}")
            //操作成功
                 .then(function(){
                     swal({
                         title:'操作成功',
                         icon:'success',
                         button:"确定"
                     }).then(function(){
                         location.reload();
                     })
                 } ,
            //操作失败
                 function(error){

                     //如果返回时401则是没有登录
                     if(error.response && error.response.status===401){
                         swal({
                             title:'请先登录',
                             icon:'error',
                             button:'确定'
                         })
                     }else if(error.response && error.response.status===403){
                        swal({
                             title:'请验证邮箱',
                             icon:'error',
                             button:'确定'
                         })
                     }
                     else if(error.response && (error.response.data.msg||error.response.data.message)){
                         swal({
                             title: error.response.data.msg ? error.response.data.msg:error.response.data.message,
                             icon: 'error',
                             button:'确定'
                         })
                     }else{
                         swal({
                             title:'系统错误',
                             icon:'error',
                             button:'确定'
                         })
                     }
                 })
        })
//取消收藏
        $('.disfavor-btn').click(function(){
            axios.delete("{{route('products.disFavorite',$product->id)}}")
                 .then(function(){
                     swal({
                         title:'操作成功',
                         icon:'success',
                         button:'确定'
                     }).then(function(){
                         location.reload()
                     })
                 })
        })


//加入购物车
        $('.anthor-btn .cart-btn').click(function(){
            axios.post("{{route('cart.add')}}",{
                sku_id:$('.sku button.active input[name=sku_id]').val(),
                amount:$('.goods-count .amount').val()
            }).then(function(){
                swal({
                    title:'添加成功',
                    icon:'success',
                }).then(function(){
                    location.href='/cart/'
                })
            }, function(error){
                if(error.response.status===401){
                    swal({
                        title:'请先登录',
                        icon:'error'
                    })
                }else if(error.response.status===422){

                    var html;
                     //第一个是数组，函数第一个参数下标，第二个参数为下标值
                     $.each(error.response.data.errors, function(index, value){
                        html=value[0];
                     })
                     swal({
                         title:html,
                         icon:'error'
                     })
                }

            })
        })
    })
</script>

@endsection
