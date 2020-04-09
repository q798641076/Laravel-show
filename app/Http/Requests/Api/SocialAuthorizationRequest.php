<?php

namespace App\Http\Requests\Api;

class SocialAuthorizationRequest extends Request
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
           'code'=>'required|string',
           'access_token'=>'required_without:code|string'
       ];
       if(!$this->code && $this->social_type="weixin"){
           $rules['openId']='required|string';
       }

       return $rules;
    }
}
