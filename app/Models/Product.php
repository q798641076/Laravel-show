<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable=[
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $cast=[
        'on_sale'=>'boolean'
    ];

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function getImageUrlAttribute()
    {
        //如果是完整路径，则返回原本字段
        if(Str::startsWith($this->attributes['image'],['http://','https://'])){
            return $this->attributes['image'];
        }
        //否则返回storage下的public路径
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function scopeOnSale($query)
    {
        return $query->where('on_sale',true);
    }

    public function scopeFormSelect($query,$like)
    {
        return $query->where('title','like',$like)
                     ->orWhere('description','like',$like)
                     ->orWhereHas('skus',function($query) use ($like){
               $query->where('title','like',$like)
                     ->orWhere('description','like',$like);
        });
    }
}
