<?php

namespace App\Http\Resources;

use App\Models\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;


class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data=parent::toArray($request);

        $user=\Auth('api')->user();

        if($user->favoriteProducts()->find($this->id)){
            $data['favorite']=true;
        }

        if($request->search || $request->order){
            $data['filter']['search']=$request->search ? :null;
            $data['filter']['order']=$request->order ? :null;
        }

        $data['image']=Product::find($this->id)->image_url;

        if(isset(request()->product->id)){
        $orderItems=OrderItem::query()
                              ->with(['order.user','product_sku'])
                              ->whereNotNull('reviewed_at')
                              ->where('product_id',$this->id)
                              ->whereHas('order',function($query){
                                $query->whereNotNull('paid_at');
                              })
                              ->get();
        $data['reviews']=OrderItemResource::collection($orderItems);
        }
        return $data;
    }
}
