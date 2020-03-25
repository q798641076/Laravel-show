<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductSku;

class AddCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules=[
            'sku_id'=>[
                'required',
                function($attribute, $value, $fail)
                {
                    if(!$sku=ProductSku::find($value)){
                        return $fail('商品类型不存在');
                    }
                    if(!$sku->product->on_sale){
                        return $fail('商品未上架');
                    }
                    if($sku->stock==0){
                        return $fail('商品已售空');
                    }
                    if($sku->stock>0 && $this->amount>$sku->stock){
                        return $fail('库存不足');
                    }
                }
            ],
            'amount'=>'required|integer|min:1'
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'amount'=>'数量'
        ];
    }

    public function messages(){

        return [
            'sku_id.required'=>'请选择商品'
        ];
    }
}
