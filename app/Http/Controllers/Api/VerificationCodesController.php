<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request,EasySms $easysms)
    {
        //图片验证是否过期
        if(!$captcha=Cache::get($request->captcha_key)){
            abort(403,'图片验证码过期');
        }
        //验证图片验证码是否正确
        if(!hash_equals($captcha['captchaCode'],$request->captcha_code)){
            throw new AuthenticationException('图片验证码错误');
        }
        //随机生成5位手机验证码
        $code=str_pad(random_int(1,99999),5,0,STR_PAD_LEFT);
        //获取电话号
        $phone=$captcha['phone'];

        if(!app()->environment('production')){
                $code='12345';
            }
        else{
            try{
                $result=$easysms->send($phone,[
                        'template'=>config('easysms.gateways.aliyun.templates.register'),
                        'data'=>[
                            'code'=>$code
                        ]
                    ]);
            }catch(\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
                    $ext=$exception->getException('aliyun')->getMessage();
                    return $ext;
            }
        }

        //存入缓存
        $verificationkey='VerificationCode_'.Str::random(15);

        $expiredAt=now()->addMinute(5);

        Cache::put($verificationkey, ['phone'=>$phone,'code'=>$code], $expiredAt);
        //删除图片验证缓存
        Cache::forget($request->captcha_key);

        return response()->json(['phone'=>$phone,'expiredAt'=>$expiredAt->toDateTimeString(),'key'=>$verificationkey],201);
    }
}
