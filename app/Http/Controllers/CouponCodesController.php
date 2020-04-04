<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function show($code)
    {
        if(!$code=CouponCode::query()->where('code',$code)->first()){
            abort('404');
        }
        if(!$code->enabled){
            abort('404');
        }
        if($code->total-$code->used<=0){
            return response()->json(['msg'=>'该优惠码已被抢光'],403);
        }
        //开始和结束时间要存在才能进行判断
        if($code->not_before && $code->not_before->gt(Carbon::now())){
            return response()->json(['msg'=>'优惠码未到使用日期'],403);
        }
        if($code->not_after && $code->not_after->lt(Carbon::now())){
            return response()->json(['msg'=>'优惠码已过期'],403);
        }
        return $code;
    }
}
