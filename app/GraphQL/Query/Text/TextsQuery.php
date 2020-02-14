<?php

namespace App\GraphQL\Query\Text;

use App\Helpers\AuthHelper;
use App\Models\Project;
use App\Models\Text;
use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;

class TextsQuery extends Query
{
    protected $attributes = [
        'name' => 'texts',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Text'));
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
            'id' => ['name' => 'id', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Text::query();

        $project_id = $args['projectId'];

        if ($project_id) {
            $query->where('project_id', $project_id);
        }

        if (isset($args['id']) && $args['id']) {
            $query->where('id', $args['id']);
        }

        return $query->get();
    }
}
