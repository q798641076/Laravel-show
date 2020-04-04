<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use Ramsey\Uuid\Uuid;
use App\Models\CouponCode;

class Order extends Model
{
    //定义常量，用来设置信息提示
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    //给常量赋值要用静态属性，用self调用
    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts=[
        'closed'=>'boolean',
        'reviewed'=>'boolean',
        'address'=>'json',
        'ship_data'=>'json',
        'extra'=>'json'
    ];

    protected $dates=[
        'paid_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function findAvailableNo()
    {
        //流水号前缀
        $prefix=date('YmdHis');

        for($i=0;$i<10;$i++){
            //随机生成6位数
            $no=$prefix.str_pad(random_int(0,999999),6,0,STR_PAD_LEFT);

            if(!$this->where('no',$no)->exists()){
                return $no;
            }
        }

       \Log::warning('find order no fail');
        return false;
    }


    public static function refundAvailableNo()
    {
        do{
            //生成随机大概率不重复退款订单号
            $no=Uuid::uuid4()->getHex();

        }while(self::query()->where('refund_no',$no)->exists());

        return $no;
    }

    public function coupon_code_id()
    {
        return $this->belongsTo(CouponCode::class,'coupon_code_id');
    }
}
