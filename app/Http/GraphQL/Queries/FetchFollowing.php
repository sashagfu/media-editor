<?php

namespace App\Http\GraphQL\Queries;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FetchFollowing
{
    public function resolve($rootValue, array $args)
    {
        if (isset($args['userId'])) {
            return User::find($args['userId'])->following;
        }

        $user = Auth::user();

        return $user->following;
    }
}
