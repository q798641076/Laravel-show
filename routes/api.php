<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->namespace('Api')->name('api.v1')->group(function(){
    //节流处理防止攻击 (登录相关)
    Route::middleware('throttle:'.config('api.rate_limits.sign'))->group(function(){
        //手机验证码
        Route::post('verifications','VerificationCodesController@store')->name('.verifications.store');
        //注册用户
        Route::post('users','UserController@store')->name('.users.store');
        //图片验证码
        Route::post('captcha','CaptchasController@store')->name('.captcha.store');

        //第三方登录
        Route::post('socials/{social_type}/authorizations','AuthorizationsController@socialStore')
              ->where('social_type','weixin')
              ->name('.authorizations.socialStore');
        //用户登录
        Route::post('authorizations','AuthorizationsController@store')->name('.authorizations.store');
        //刷新用户jwt
        Route::put('authorizations/current','AuthorizationsController@update')->name('.authorizations.update');
        //删除用户jwt
        Route::delete('authorizations/current','AuthorizationsController@destroy')->name('.authorizations.destroy');
    });

    //(访问相关)
    Route::middleware('throttle:'.config('api.rate_limits.access'))->group(function(){

    });
});
