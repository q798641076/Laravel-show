<?php

namespace App\Http\Resources;

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

        return $data;
    }
}
