@extends('layouts.app')

@section('content')
 <div class="row">
     <div class="col-lg-10 col-md-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                地址信息
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
                        <button class="btn btn-success">编辑</button>
                        <button class="btn btn-danger">删除</button>
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
