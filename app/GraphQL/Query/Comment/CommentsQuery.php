<?php

namespace App\GraphQL\Query\Comment;

use App\Models\Comment;
use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;

class CommentsQuery extends Query
{
    const PAGINATION_COUNT = 10;

    protected $attributes = [
        'name' => 'comments',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Comment'));
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Comment::query();

        if ($args['projectId']) {
            $query->where('project_id', $args['projectId']);
        }

        $query->where('parent_id', null);

        return $query->get();
    }
}
