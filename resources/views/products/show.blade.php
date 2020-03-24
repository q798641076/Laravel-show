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
                                        <input type="radio"  name="skus" value="{{$sku->id}}" autocomplete="off">
                                        {{$sku->title}}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="goods-count">
                            <label>数量</label>
                            <input type="text" class="form-control form-control-sm" value="1">
                            <span>件</span><span class="stock"></span>
                        </div>

                        <div class="btn-group anthor-btn">
                            @if ($favorite)
                            <button class="btn btn-danger disfavor-btn">取消收藏</button>
                            @else
                            <button class="btn btn-success favor-btn">❤收藏</button>
                            @endif
                            <button class="btn btn-primary">加入购物车</button>
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
                        {{$product->description}}
                    </div>
                    <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">...</div>
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
                     console.log(error.response)
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

    })
</script>

@endsection
