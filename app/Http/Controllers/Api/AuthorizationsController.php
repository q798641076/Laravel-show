<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    //用户登录
    public function store(AuthorizationRequest $request)
    {
        $username=$request->input('username');

        filter_var($username,FILTER_VALIDATE_EMAIL) ?
        $user['email']=$username:
        $user['phone']=$username;

        $user['password']=$request->password;

        //验证是否存在该用户，如果存在返回一个token
        if(!$token=\Auth::guard('api')->attempt($user)){
            throw new AuthorizationException('账号密码错误');
        }

        return $this->responseToken($token)->setStatusCode(201);
    }

    //第三方登录
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $type=$request->social_type;

        $driver=\Socialite::driver($type);

        try{
                //如果code存在，证明是将accesstoken存储到服务器中的
                if($code=$request->code){

                    $response=$driver->getAccessTokenResponse($code);

                    $accessToken=$response['access_token'];

                    $openId=$response['openid'];
                }else{
                    $accessToken=$request->access_token;

                    if($type=='weixin'){
                        //发送openId 只有微信要
                        $driver->setOpenId($openId);
                    }
                }
                //获取用户信息
                $authUser=$driver->userFromToken($accessToken);

        }catch(\Exception $e){
            throw new AuthorizationException('验证错误');
        }
        //验证是否存在
        switch($type)
        {
            case 'weixin':
                $unionid=$authUser->offsetExists('unionid') ? $authUser->offsetGet('unionid'): null;

                if(!$unionid){
                    $user=User::query()->where('weixin_openid',$authUser->getId())->first();
                }else{
                    $user=User::query()->where('weixin_unionid',$authUser->offsetGet('unionid'))->first();
                }

                if(!$user){
                    $user=User::create([
                        'name'=>$authUser['nickname'],
                        'weixin_openid'=>$authUser->getId(),
                        'weixin_unionid'=>$unionid
                    ]);
                }
            break;
        }

        $token=auth('api')->login($user);

        return $this->responseToken($token)->setStatusCode(201);
    }

    //刷新
    public function update()
    {
        $token=auth('api')->refresh();

        return $this->responseToken($token)->setStatusCode(201);
    }

    //删除
    public function destroy()
    {
        auth('api')->logout();

        return response(null,204);
    }

    public function responseToken($token)
    {
        return response()->json([
            'jwt'=>$token,
            'expire_in'=>\Auth::guard('api')->factory()->getTTL()*60,
            'token_type'=>'Bearer'
        ]);
    }
}
