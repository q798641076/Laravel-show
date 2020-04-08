<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends Request
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
        return [
            'name'=>'required',
            //alpha_dash要求输入破折号之类的
            'password'=>'required|alpha_dash|min:6',
            'verification_key'=>'required|string',
            'verification_value'=>'required|string'
        ];
    }
}
