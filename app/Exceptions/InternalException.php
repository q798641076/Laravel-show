<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

class InternalException extends Exception
{
    //第一个参数是错误信息，第二个参数是返回给用户界面的错误信息
    public function __construct(string $message, string $msgForUser='系统内部错误', $code=500)
    {
        parent::__construct($message,$code);

        $this->msgForUser=$msgForUser;
    }

    public function render(Request $request)
    {
        if($request->expectsJson()){
            return response()->json(['msg'=>$this->msgForUser],$this->code);
        }

        return view('pages.error',['msg'=>$this->msgForUser]);
    }
}
