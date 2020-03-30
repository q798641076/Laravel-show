@extends('layouts.app')

@section('title',$msg)

@section('content')
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                支付成功
            </div>
            <div class="card-body">
                <p class="text-center">{{$msg}}
                    <a href="{{route('products.index')}}" class="btn btn-success">回到首页</a>
                </p>
            </div>
        </div>
    </div>
@endsection
