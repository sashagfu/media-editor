<?php

namespace App\GraphQL\Query\Project;

use App\Models\Project;
use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;

class ProjectsQuery extends Query
{
    protected $attributes = [
        'name' => 'projects',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Project'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'authorId' => ['name' => 'authorId', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Project::query();

        if (isset($args['authorId']) && $args['authorId']) {
            $query->where('author_id', $args['authorId']);
        }

        if (isset($args['id']) && $args['id']) {
            $query->where('id', $args['id']);
        }

        return $query->get();
    }
}
