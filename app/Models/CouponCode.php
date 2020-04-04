<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    const TYPE_FIXED='fixed';
    const TYPE_PERCENT='percent';

    public static $couponTypeMap=[
        self::TYPE_FIXED=>'固定金额',
        self::TYPE_PERCENT=>'百分比金额'
    ];

    protected $fillable=[
        'name',
        'type',
        'code',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];

    protected $dates=[
        'not_before',
        'not_after'
    ];

    protected $casts=[
        'enabled'=>'boolean'
    ];

    //生成随机16位优惠卷码 大写
    public static function findAvailableCode($length=16)
    {
        do{
            $code=strtoupper(Str::random($length));
        }while(self::query()->where('code',$code)->exists());

        return $code;
    }
    //添加一个数据库属性
    //转换模型到 数组 或 JSON 在控制器用访问器时需要
    protected $appends=['description'];
    //访问器
    public function getDescriptionAttribute()
    {
        $str='';
        if($this->min_amount>0){
            $str='满'.str_replace('.00','',$this->min_amount);
        }
        if($this->type===self::TYPE_FIXED){
           return $str.'减'.str_replace('.00','',$this->value);
        }
        return $str.'优惠'.str_replace('.00','',$this->value).'%';
    }
}
