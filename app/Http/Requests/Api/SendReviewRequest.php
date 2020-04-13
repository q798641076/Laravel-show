<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends Request
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
            'reviews'=>'required|array',

            'reviews.*.id'=>[
                'required',
                Rule::exists('order_items','id')->where('order_id',$this->route('order')->id),
                'integer'
            ],

            'reviews.*.rating'=>'required|integer|between:1,5',

            'reviews.*.review'=>'required|max:80'
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'reviews.*.id'=>'订单项',
            'reviews.*.rating'=>'评分',
            'reviews.*.review'=>'评价'
        ];
    }
}
