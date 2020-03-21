<?php
namespace App\Repositories;

class UserAddressRepository
{
    public function index($request)
    {
        return  $request->user()->addresses;
    }

    public function store($request)
    {
        $request->user()->addresses()->create($request->only([
            'province','city','district','address','zip','contact_name','contact_phone'
        ]));
    }

    public function update($user_address,$request)
    {
        $user_address->update($request->only([
            'province','city','district','address','zip','contact_name','contact_phone'
        ]));
    }

    public function delete($user_address)
    {
        $user_address->delete();
    }

}
