<?php

namespace App\Http\Requests;



class UserAddressRequest extends Request
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
        return [
            'contact_name'      =>'required',
            'contact_phone'     =>'required|integer',
            'address'           =>'required',
            'province'          =>'required',
            'city'              =>'required',
            'district'          =>'required',
            'zip'               =>'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'contact_name'      =>'姓名',
            'contact_phone'     =>'电话',
            'address'           =>'详细地址',
            'province'          =>'省',
            'city'              =>'市',
            'district'          =>'区',
            'zip'               =>'邮政编号'
        ];
    }
}
