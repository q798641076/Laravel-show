<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CouponCodeUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CouponCodeResource;
use App\Models\CouponCode;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function show($code, Request $request)
    {
        if(!$code=CouponCode::query()->where('code',$code)->first()){
            throw new CouponCodeUnavailableException('优惠卷不存在');
        }

        $code->checkCouponCode($request->user());

        return new CouponCodeResource($code);
    }
}
