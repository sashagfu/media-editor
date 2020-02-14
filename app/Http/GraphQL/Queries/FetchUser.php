<?php

namespace App\Http\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchUser
{
    public function resolve($root, $args)
    {
        if (isset($args['username'])) {
            return User::whereUsername($args['username'])->first();
        } else {
            return Auth::user();
        }
    }
}
