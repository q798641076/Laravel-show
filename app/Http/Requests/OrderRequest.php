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
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)
            ],
            'items'=>'required|array',
            // 检查 items 数组下每一个子数组的 sku_id 参数
            'items.*.sku_id'=>[
                'sku_id'=>[
                    'required',
                    function($attribute,$value,$fail){
                        if(!$sku=ProductSku::find($value)->first()){
                            return $fail('商品不存在');
                        }
                        if(!$sku->on_sale){
                            return $fail('商品未上架');
                        }
                        if($sku->stock==0){
                            return $fail('商品已售完');
                        }
                        //在检查库存时，我们需要获取用户想要购买的该 SKU 数量，我们可以通过匿名函数的第一个参数 $attribute
                        //来获取当前 SKU 所在的数组索引，比如第一个 SKU 的 $attribute 就是 items.0.sku_id，
                        //所以我们采用正则的方式将这个 0 提取出来，$this->input('items')[0]['amount'] 就是用户想购买的数量。
                        //获取当前索引
                        preg_match('/items\.(\d+)\.sku_id/',$attribute,$m);
                        $index=$m[0];
                        $amount=$this->items[$index]['amount'];
                        if($amount>0 && $amount>$sku->stock){
                            return $fail('库存不足');
                        }
                    }
                ],
            ],
            'items.*.amount'=>'required|min:1|integer',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'items'=>'提交商品',
            'amount'=>'商品数量'
        ];
    }
}
