<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAddress;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Notifiable;

    //返回的是我们用户的id
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    //需要额外再JWT载荷中增加自定义内容
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','weixin_openid','weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','weixin_unionid','weixin_openid'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addresses()
    {
        return $this->hasMany(UserAddress::class,'user_id');
    }

    //收藏商品
    public function favoriteProducts()
    {
        //默认的中间表是不创建时间，需要自己设置withTimestamps
        return $this->belongsToMany(Product::class, 'user_favorite_products')
                    ->withTimestamps()
                    ->orderBy('created_at','desc');
    }

    //购物车
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    //订单
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
