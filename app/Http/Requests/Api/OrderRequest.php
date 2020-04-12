<?php

namespace App\Http\Requests\Api;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules=[
            'address_id'=>[
                'required',
                Rule::exists('user_addresses','id')->where('id',\Auth('api')->user()->id)
            ],
            'items'=>'required|array',
            // items.0.sku_id
            'items.*.sku_id'=>[
                'required',
                function($attribute,$value,$fail){
                    if(!$productSku=ProductSku::find($value)){
                        return $fail('该商品不存在');
                    }
                    if(!$productSku->product->on_sale){
                        return $fail('商品未上架');
                    }
                    if($productSku->stock<=0){
                        return $fail('该商品卖完了');
                    }
                    preg_match('/items\.(\d+)\.sku_id/',$attribute,$m);
                    $index=$m[1];
                    $amount=$this->input('items')[$index]['amount'];
                    if($amount>0 && $amount>$productSku->stock){
                        return $fail('库存不够');
                    }

                }
            ],
            'items.*.amount'=>'required|integer|numeric|min:1',

            'remark'=>'string|max:80'
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'address_id'=>'地址',
            'remark'=>'备注',
            'items'=>'商品'
        ];
    }
}
