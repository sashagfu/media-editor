<?php

namespace App\Http\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchNotifications
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        return $user->notifications()
            ->skip($args['skip'])
            ->take(6)
            ->get();
    }
}
