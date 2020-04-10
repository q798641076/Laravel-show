<?php

namespace App\Http\Requests\Api;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;

class AddCartRequest extends Request
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
        switch($this->method()){
            case 'POST':
                $rules=[
                'sku_id'=>[
                    'required',
                    function($attribute,$value,$fail){
                        if(!$productSku=ProductSku::find($value)){
                            return $fail('商品不存在');
                        }
                        if(!$productSku->product->on_sale){
                            return $fail('商品未上架');
                        }
                        if($productSku->stock<=0){
                            return $fail('该商品卖完了');
                        }
                        if($this->amount && $this->amount>$productSku->stock){
                            return $fail('商品库存不足');
                        }
                    }
                ],
                'amount'=>'required|integer|min:1'
            ];
            break;

            case 'PATCH':
                $rules=[
                    'amount'=>'required|integer|min:1|numeric'
                ];
            break;
        }


        return $rules;
    }

    public function attributes()
    {
        return [
            'sku_id'=>'请选择商品',
            'amount'=>'请输入数量'
        ];
    }
}
