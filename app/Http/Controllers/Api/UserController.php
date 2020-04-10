<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    //手机注册
    public function store(UserRequest $request)
    {
        $key=Cache::get($request->verification_key);

        if(!$key){
            abort(403,'验证码失效');
        }

        if(!hash_equals($key['code'],$request->verification_value)){

            throw new AuthenticationException('验证码错误');
        }

        $user=User::create([
            'name'=>$request->name,
            'phone'=>$key['phone'],
            'password'=>bcrypt($request->password)
        ]);

        Cache::forget($request->verification_key);

        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
