<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class EasySmsServericeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //向服务容器里注入EasySms类
        $this->app->singleton(EasySms::class,function($app){
            return new EasySms(config('easysms'));
        });
        //给这个类取个别名
        $this->app->alias(EasySms::class,'easysms');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
