<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        $addresses=$request->user()->addresses;

        return UserAddressResource::collection($addresses);
    }

    public function store(UserAddressRequest $request)
    {
        $address=$request->user()->addresses()->create(
            $request->only(['province','city','district','zip','contact_name','contact_phone','address'])
        );

        return new UserAddressResource($address);
    }

    public function update(UserAddress $address, UserAddressRequest $request)
    {
        $this->authorize('update',$address);

        $address->update($request->all());

        return new UserAddressResource($address);
    }

    public function delete(UserAddress $address)
    {
        $this->authorize('delete',$address);

        $address->delete($address);

        return response(null,204);
    }
}
