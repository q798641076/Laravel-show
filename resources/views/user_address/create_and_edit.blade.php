@extends('layouts.app')
@if (isset($address->id))
@section('title', '修改收货地址')
@else
@section('title', '新增收货地址')
@endif


@section('content')
<div class="row">
<div class="col-md-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h2 class="text-center">
        @if (isset($address->id))
        修改收货地址
        @else
        新增收货地址
        @endif

    </h2>
  </div>
  <div class="card-body">
    <!-- 输出后端报错开始 -->
    @if (count($errors) > 0)
      <div class="alert alert-danger alert-dismissible fade show ">
        <ul>
          @foreach ($errors->all() as $error)
            <li> <button class="close" data-dismiss="alert">&times;</button> {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <!-- 输出后端报错结束 -->
    <!-- inline-template 代表通过内联方式引入组件 -->
    @include('user_address._form')
  </div>
</div>
</div>
</div>
@endsection
