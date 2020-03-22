<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use App\Repositories\UserAddressRepository;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    protected $pro;

    public function __construct(UserAddressRepository $pro)
    {
        $this->pro=$pro;
    }

    public function index(Request $request)
    {
        $addresses=$this->pro->index($request);

        return view('user_address.index',compact('addresses'));
    }

    public function create(UserAddress $user_address)
    {

        return view('user_address.create_and_edit',['address'=>$user_address]);
    }

    public function store(UserAddressRequest $request)
    {

        $this->pro->store($request);

        return redirect()->route('user_addresses.index')->with('success','添加成功');

    }

    public function edit(UserAddress $user_address)
    {

        // $address=UserAddress::findOrFail($id);

        return view('user_address.create_and_edit',['address'=>$user_address]);
    }

     public function update(UserAddress $user_address, UserAddressRequest $request)
    {
        $this->authorize('update', $user_address);

        $this->pro->update($user_address, $request);

        return redirect()->route('user_addresses.index')->with('success','修改成功');
    }

    public function destroy(UserAddress $user_address)
    {
        $this->authorize('delete',$user_address);

        $this->pro->delete($user_address);

        return ['message'=>'删除成功！'];
    }


}
