<?php

namespace App\Providers;

use Monolog\Logger;
use Illuminate\Support\ServiceProvider;
use Yansongda\Pay\Pay;
use Illuminate\Http\Resources\Json\Resource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //往服务容器中注入一个名为alipay的单例对象
        $this->app->singleton('alipay',function(){
            $config=config('pay.alipay');
            //notify_url服务器回调，return_url浏览器回调
            //值必须是完整的域名
            $config['return_url']=route('alipay.return');
            $config['notify_url']='http://requestbin.net/r/1bzq9ee1';
            //判断当前项目运行环境是否为线上环境
            if(app()->environment()!=='production'){
                $config['mode']='dev';
                $config['log']['level']=Logger::DEBUG;
            }else{
                $config['log']['level']=Logger::WARNING;
            }
            //调用Yansongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay',function(){
            $config=config('pay.wechat');
            //判断当前项目运行环境是否为线上环境
            if(app()->environment()!=='production'){
                $config['log']['level']=Logger::DEBUG;
            }else{
                $config['log']['level']=Logger::WARNING;
            }
            //调用Yansongda\Pay来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //为Order模型注册Observe
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);

        //去掉 data 这一层包裹。
        Resource::withoutWrapping();
    }
}
