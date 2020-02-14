<?php

namespace App\Http\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchSavedAssets
{
    public function resolve()
    {
        $user = Auth::user();
        return $user->savedAssets;
    }
}
