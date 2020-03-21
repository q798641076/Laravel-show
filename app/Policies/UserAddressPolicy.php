<?php

namespace App\Policies;

use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
class UserAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function update(User $user, UserAddress $address)
    {
        return $user->id==$address->user_id;
    }
    public function delete(User $user, UserAddress $address)
    {
        return $user->id==$address->user_id;
    }
}
