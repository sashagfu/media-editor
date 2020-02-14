<?php

namespace App\Http\GraphQL\Queries;

use Illuminate\Support\Facades\Auth;

class FetchMe
{
    public function resolve($rootValue, array $args)
    {
        return Auth::user();
    }
}
