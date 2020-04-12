<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

        $data['order_items']=OrderItemResource::collection($this->whenLoaded('orderItems'));
        $data['coupon_code']=new CouponCodeResource($this->whenLoaded('couponCode'));
        return $data;
    }
}
