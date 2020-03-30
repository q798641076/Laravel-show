<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'address_id'=>[
                'required',
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id),
            ],
            'items'=>'required|array',
            //判断items下面的索引值 索引为items.[].sku_id||amount ==$attribute
            'items.*.sku_id'=>[
                    'required',
                    function($attribute,$value,$fail){
                        if(!$sku=ProductSku::findOrFail($value)){
                            return $fail('商品不存在');
                        }
                        if(!$sku->product->on_sale){
                            return $fail('商品已经下架了');
                        }
                        if($sku->stock==0){
                            return $fail('商品卖光啦！');
                        }
                        preg_match('/items\.(\d+)\.sku_id/',$attribute,$m);
                        $index=$m[1];
                        $amount=$this->items[$index]['amount'];
                        if($amount>0&&$amount>$sku->stock){
                            return $fail('商品库存不足');
                        }
                    }
            ],

            'items.*.amount'=>'required|integer|min:1'
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'address_id'=>'地址',
            'items'=>'提交商品',
            'amount'=>'商品数量'
        ];
    }
}
