<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserAddress;
use Faker\Generator as Faker;

$factory->define(UserAddress::class, function (Faker $faker) {

    $addresses=[
        ['广东省','深圳市','南山区'],
        ["北京市", "市辖区", "东城区"],
        ["河北省", "石家庄市", "长安区"],
        ["江苏省", "南京市", "浦口区"],
        ["江苏省", "苏州市", "相城区"],
    ];
    $address=$faker->randomElement($addresses);

    return [
        'province'=>$address[0],
        'city'=>$address[1],
        'district'=>$address[2],
        'address'=>sprintf('%d街道%s号',$faker->randomNumber(2),$faker->randomNumber(3)),
        'contact_name'=>$faker->name,
        'contact_phone'=>$faker->phoneNumber,
        'zip'=>$faker->postcode,
    ];
});
