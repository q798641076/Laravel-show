<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses=$request->user()->addresses;

        return view('user_address.index',compact('addresses'));
    }
}
