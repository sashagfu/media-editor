<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AlgoliaSearch\Client as Algolia;

class FetchProject
{

    public function resolve($rootValue, array $args)
    {
        if (isset($args['uuid'])) {
            return Project::whereUuid($args['uuid'])->first();
        }

        return Project::findOrFail($args['id']);
    }
}
