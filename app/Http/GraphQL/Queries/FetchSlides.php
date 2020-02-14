<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Slide;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchSlides
{
    public function resolve($rootValue, array $args)
    {
        $query = Slide::query();

        $query->where('project_id', $args['id']);

        return $query->get();
    }
}
