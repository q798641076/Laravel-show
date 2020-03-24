@extends('layouts.app')

@section('title','错误')

@section('content')
    <div class="card">
        <div class="card-header">
           发生错误
        </div>
        <div class="card-body">
            <div class="text-center">
                <h3 class="text-danger">{{$msg}}</h3>
                <a class="btn btn-primary" href="{{route('/')}}">返回首页</a>
            </div>
        </div>
    </div>
@endsection
