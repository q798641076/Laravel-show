<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserAddress extends Model
{
    protected $fillable=[
        'province','city','district','address','zip',
        'contact_name','contact_phone', 'last_used_at'
    ];

    //将这个属性转化为Carbon类属性
    protected $dateTime=['last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function getFullAddressAttribute()
    {
        return $this->province.$this->city.$this->district.$this->address;
    }
}
