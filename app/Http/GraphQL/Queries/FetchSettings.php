<?php

namespace App\Http\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchSettings
{
    public function resolve($rootValue, array $args)
    {
        return Auth::user()->settings;
    }
}
