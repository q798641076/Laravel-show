@extends('layouts.app')
@section('title','评价商品')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    商品评价
                    <a href="{{route('orders.index')}}" class="float-right">返回订单列表</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>商品名称</th>
                            <th>打分</th>
                            <th>评价</th>
                        </tr>
                        @foreach ($order->orderItems as $index=>$item)
                            <tr data-id={{$item->id}}>
                                <td>
                                    <div class="product-info">
                                        <div class="preview">
                                            <a href="{{route('products.show',$item->product_id)}}">
                                                <img src="{{$item->product->image_url}}" alt="">
                                            </a>
                                        </div>
                                        <div class="product-title">
                                            {{$item->product->title}} &nbsp;
                                        </div>
                                        <div class="sku-title">
                                            {{$item->product_sku->title}}
                                        </div>
                                    </div>
                                </td>
                                <td class="vertical-middle">
                                    @if ($order->reviewed)
                                    <span class="vertical-middle">
                                        <span class="rating-star-yes">{{ str_repeat('★', $item->rating) }}</span>
                                        <span class="rating-star-no">{{ str_repeat('★', 5 - $item->rating) }}</span>
                                    </span>
                                    @else
                                    <ul class="rate-area">
                                        <input type="radio" id="5-star-{{$index}}" name="rating{{$index}}" value="5" checked>
                                        <label for="5-star-{{$index}}"></label>
                                        <input type="radio" id="4-star-{{$index}}" name="rating{{$index}}" value="4">
                                        <label for="4-star-{{$index}}"></label>
                                        <input type="radio" id="3-star-{{$index}}" name="rating{{$index}}" value="3">
                                        <label for="3-star-{{$index}}"></label>
                                        <input type="radio" id="2-star-{{$index}}" name="rating{{$index}}" value="2">
                                        <label for="2-star-{{$index}}"></label>
                                        <input type="radio" id="1-star-{{$index}}" name="rating{{$index}}" value="1">
                                        <label for="1-star-{{$index}}"></label>
                                    </ul>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->reviewed)

                                            {{$item->review}}

                                    @else
                                        <textarea name="review" id="" cols="8" class="form-control @if($errors->has("reviews[$index]['review']")) has-error @endif"></textarea>
                                        <span class="block">{{$errors->first("reviews[$index]['review']")}}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="text-center" colspan="3">
                                @if ($order->reviewed)
                                <a class="btn btn-primary btn-sm" href="{{route('orders.show',$order->id)}}">查看订单</a>
                                @else
                                <button class="btn btn-primary btn-sm btn-review">提交评论</button>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptAfterJs')
    <script>
        $(document).ready(function(){
            $('.btn-review').click(function(){
                $rad={reviews:[]};
                $('table tr[data-id]').each(function(){

                    $rating=$(this).find('.rate-area input[type=radio]:checked').val();

                    $review=$(this).find('textarea[name=review]').val();

                    $rad.reviews.push({
                        id:$(this).data('id'),
                        rating:$rating,
                        review:$review
                    });
                })

                axios.post("/orders/{{$order->id}}/review",$rad)
                     .then(function(){
                         swal('评论成功','','success');
                         location.reload();
                     },function(error){
                        if(error.response.status===422){
                            $.each(error.response.data.errors,function(index,value){
                                html=value[0]
                            });
                            swal({title:html,icon:'error'})
                        }else{
                            swal('系统错误','','error');
                    }
                })
            })
        })
    </script>
@endsection
