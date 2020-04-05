<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

class CouponCodeUnavailableException extends Exception
{
    public function __construct(string $message,$code=403)
    {
        parent::__construct($message,$code);
    }

    public function render(Request $request)
    {
        //如果是api接口调用
        if($request->expectsJson()){
            return response()->json([$this->message],$this->code);
        }
        return redirect()->back()->withErrors(['coupon_code'=>$this->message]);
    }
}
