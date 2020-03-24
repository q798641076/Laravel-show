@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
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
                        {{$products->render()}}
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
