<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Comment;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchComments
{
    public function resolve($rootValue, array $args)
    {
        $query = Comment::query();

        if ($args['id']) {
            $query->where('project_id', $args['id']);
        }

        $query->where('parent_id', null);

        return $query->get();
    }
}
