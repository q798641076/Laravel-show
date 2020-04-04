<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CouponCode;
use Faker\Generator as Faker;

$factory->define(CouponCode::class, function (Faker $faker) {

    //优惠卷类型
    $type=$faker->randomElement(array_keys(CouponCode::$couponTypeMap));

    //面值
    $value=$type===CouponCode::TYPE_FIXED ? random_int(20,50) : random_int(1,5);

    if($type==CouponCode::TYPE_FIXED){
        $min_amount=$value+100;
    }else{
         // 如果是百分比折扣，有 50% 概率不需要最低订单金额
         if(random_int(0,100)<50){
             $min_amount=0;
         }else{
             $min_amount=random_int(100,500);
         }
    }

    return [
        'name'=>$faker->word,
        'code'=>CouponCode::findAvailableCode(),
        'type'=>$type,
        'total'=>100,
        'used'=>0,
        'value'=>$value,
        'min_amount'=>$min_amount,
        'not_before'=>null,
        'not_after'=>null,
        'enabled'=>true
    ];
});
