@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                {!! Form::open(['route'=>['products.index'],'class'=>"search-form",'method'=>'get']) !!}
                    <div class="form-row formSelect">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-auto">
                                    {!! Form::text('search', null,['class'=>'form-control form-control-sm',
                                    'placeholder'=>'搜索']) !!}
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary btn-sm">搜索</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            {!! Form::select('order',[
                                'price_asc'=>'价格从低到高',
                                'price_desc'=>'价格从高到低',
                                'sold_count_asc'=>'销量从低到高',
                                'sold_count_desc'=>'销量从高到低',
                                'rating_desc'=>'评价从高到低',
                                'rating_asc'=>'评价从低到高'
                            ], null ,['placeholder'=>'排序方式','class'=>'form-control form-control-sm']) !!}
                        </div>
                    </div>
                {!! Form::close() !!}
                <div class="row products-list">
                @foreach ($products as $product)
                    <div class="col-md-3 product-item">
                        <div class="product-content">
                            <div class="top">
                                <div class="img">
                                   <a href="{{route('products.show',$product->id)}}"><img src="{{$product->image_url}}" alt=""></a>
                                </div>
                                <div class="price"><b>￥</b>{{$product->price}}</div>

                                <div class="title">
                                  <p><a href="{{route('products.show',$product->id)}}"> {{$product->title}}</a></p>
                                </div>
                            </div>
                            <div class="bottom">
                                <div class="sold_count">销量:<span>{{$product->sold_count}}笔</span></div>
                                <div class="review_count">评论数:<span>{{$product->review_count}}</span></div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                    <div class="float-right pagination">
                        {{$products->appends($filters)->render()}}
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection

@section('scriptAfterJs')
    <script>
       $(document).ready(function() {
           //要将后台传来的数组变成json形式
           var filters={!! json_encode($filters) !!}
           $('.search-form input[name=search]').val(filters.search);
           $('.search-form select[name=order]').val(filters.order);

           $('.search-form select[name=order]').change(function(){
               $('.search-form').submit();
           })
       });
    </script>
@endsection
