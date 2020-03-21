@extends('layouts.app')

@section('content')
 <div class="row">
     <div class="col-lg-10 col-md-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                地址信息
                <span class="float-right">
                    <a href="{{route('user_addresses.create')}}">
                        <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;新增地址
                    </a>
                </span>
            </div>
            <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                    <th>收货人</th>
                    <th>收货人电话</th>
                    <th>地址</th>
                    <th>邮政编码</th>
                    <th>操作</th>
                    </tr>
                </thead>
                @if (count($addresses))
                @foreach ($addresses as $address)
                <tr>
                    <td>{{$address->contact_name}}</td>
                    <td>{{$address->contact_phone}}</td>
                    <td>{{$address->full_address}}</td>
                    <td>{{$address->zip}}</td>
                    <td>
                        <a class="btn btn-success float-left"
                        href="{{route('user_addresses.edit',$address->id)}}"><i class="fa fa-cog"></i>
                        </a>
                        <span class="float-left ml-3 ">
                        <button class="btn btn-danger btn-delete" data-id="{{$address->id}}"><i class="fa fa-trash"></i></button>
                        </span>
                    </td>
                </tr>
                @endforeach
                @endif

                </table>
            </div>
        </div>
     </div>

 </div>

@endsection

@section('scriptAfterJs')
<script>
    $(document).ready(function(){
        $('.btn-delete').click(function(){
        // 获取按钮上的data-id属性的值
        var id=$(this).data('id');

        //调用sweetalert
        swal({
            title:"确认删除该项吗",
            icon:"warning",
            buttons:['取消','确定'],
            dangerMode:true,
        })
        .then(function(willDelete){
        // 用户点击按钮后会触发这个回调函数
        // 用户点击确定 willDelete 值为 true， 否则为 false
        // 用户点了取消，啥也不做
        if(!willDelete){
            return;
        }
        //调用删除接口，用id来拼接出请求的url
        axios.delete('/user_addresses/'+id)
            .then(function(){
                //请求成功后：
                swal({
                    title:"删除成功啦",
                    icon:"success",
                    button:'确定',
                    text:"该地址挥之而去",
                }).then(function(){
                    location.reload();
                })
            })
        })
    })
})

</script>
@endsection
