<?php

namespace App\Helpers;

use App\Role;
use Illuminate\Support\Facades\Auth;

final class AuthHelper
{
    public static function getSiteAdmins()
    {
        return self::getAdminRole()->users()->get();
    }

    public static function getAdminRole()
    {
        return Role::where('name', '=', 'admin')->first();
    }

    public static function me()
    {
        return Auth::user() ? Auth::user() : null;
    }

    public static function myId()
    {
        return Auth::user() ? Auth::user()->id : null;
    }

    public static function myName()
    {
        return Auth::user()->name;
    }
}
