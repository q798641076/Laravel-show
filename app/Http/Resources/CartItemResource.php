<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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

        $data['product_sku']=new ProductSkuResource($this->whenLoaded('productSku'));
        $data['product']=new ProductResource($this->whenLoaded('productSku.product'));
        return $data;
    }
}
