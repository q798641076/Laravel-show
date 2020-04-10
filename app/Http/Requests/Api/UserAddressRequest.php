<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends Request
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
                return [
                    'province'      =>'required|string',
                    'city'          =>'required|string',
                    'district'      =>'required|string',
                    'zip'           =>'required|integer',
                    'contact_name'  =>'required|string',
                    'contact_phone' =>'required|integer',
                    'address'       =>'required|string'
                ];
            break;

            case 'PUT':
                return [
                    'province'      =>'string',
                    'city'          =>'string',
                    'district'      =>'string',
                    'zip'           =>'integer',
                    'contact_name'  =>'string',
                    'contact_phone' =>'integer',
                    'address'       =>'string'
                ];
            break;
        }

    }

    public function attributes()
    {
        return [
            'province'      =>'省',
            'city'          =>'市',
            'district'      =>'区',
            'zip'           =>'邮编',
            'contact_name'  =>'联系人',
            'contact_phone' =>'联系电话',
            'address'       =>'详细地址'
        ];
    }
}
