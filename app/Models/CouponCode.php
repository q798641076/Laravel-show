<?php

namespace App\Models;

use App\Exceptions\CouponCodeUnavailableException;
use Carbon\Carbon;
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

    //检查优惠卷
    public function checkCouponCode($total_amount=null)
    {
        if(!$this->enabled){
            throw new CouponCodeUnavailableException('优惠卷不存在');
        }
        if($this->total<=$this->used){
            throw new CouponCodeUnavailableException('优惠卷已派完');
        }
        if($this->not_before && $this->not_before->gt(Carbon::now())){
            throw new CouponCodeUnavailableException('未到使用期间');
        }
        if($this->not_after && $this->not_after->lt(Carbon::now())){
            throw new CouponCodeUnavailableException('优惠卷已过期');
        }

        if($total_amount && $total_amount<$this->min_amount){
            throw new CouponCodeUnavailableException('未达到使用金额');
        }
    }

    //减增优惠卷库存
    public function couponCount($count=true)
    {
        //默认是true，进行使用量++
        if($count){
            //这样可以符和高并发环境
            return $this->where('id',$this->id)->where('used','<',$this->total)->increment('used');
        }
        //否则使用量--
        return $this->decrement('used');
    }

    //计算折扣后的金额
    public function getAdjustedPrice($total_amount)
    {
        //max()取最大值，避免金额小于0.01
        if($this->type===self::TYPE_FIXED){
            return max('0.01',$total_amount-$this->value);
        }
        return $total_amount-$total_amount*$this->value/100;
    }
}
