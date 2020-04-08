<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CaptchaRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request,CaptchaBuilder $captchaBuilder)
    {
        $phone=$request->phone;
        //获取完整的图片验证
        $captcha=$captchaBuilder->build();
        //随机key值
        $captchaKey='captcha_'.Str::random(15);
        //过期时间
        $expiredAt=Carbon::now()->addMinute(5);
        //将图片验证值，存入缓存
        //$captcha->getPhrase()获取值
        Cache::put($captchaKey,['phone'=>$phone,'captchaCode'=>$captcha->getPhrase()],$expiredAt);

        $result=[
            'captchaKey'=>$captchaKey,
            'captcha_image_content'=>$captcha->inline(),
            'code'=>$captcha->getPhrase(),
            'expired'=>$expiredAt->toDateTimeString()
        ];

        return response()->json($result,201);
    }
}
