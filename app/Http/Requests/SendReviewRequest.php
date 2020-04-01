<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends FormRequest
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
            'reviews'=>'required|array',
            'reviews.*.id'=>[
                'required',
                Rule::exists('order_items','id')->where('order_id',$this->route('order')->id),
            ],
            'reviews.*.rating'=>'required|between:1,5|integer',
            'reviews.*.review'=>'required|max:80',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'reviews.*.review'=>'评价',
            'reviews.*.rating'=>'评分',
            'reviews.*.id'=>'商品',
        ];
    }
}
