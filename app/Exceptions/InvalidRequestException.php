<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

class InvalidRequestException extends Exception
{
    public function __construct($message="", $code=400)
    {
        parent::__construct($message,$code);
    }

    //用来解决错误返回的信息
    public function render(Request $request)
    {
        //判断是否为ajax请求，如果时的话返回json数据
        if($request->expectsJson){
            return response()->json(['msg'=>$this->message],$this->code);
        }

        return view('pages.error',['msg'=>$this->message]);
    }
}
