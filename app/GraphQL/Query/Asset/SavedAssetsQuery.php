<?php

namespace App\GraphQL\Query\Asset;

use App\Helpers\AuthHelper;
use App\Models\Asset;
use App\Models\Project;
use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;

class SavedAssetsQuery extends Query
{
    protected $attributes = [
        'name' => 'assets',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Asset'));
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
            'version' => ['name' => 'version', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        if (isset($args['projectId'])) {
            $project = Project::findOrFail($args['projectId']);

            $assets = $project->assets();

            if (isset($args['version'])) {
                return $assets->where('version', $args['version'])->get();
            }

            return $project->assets()->where('version', $project->version)->get();
        }

        $user = AuthHelper::me();

        $assets = $user->savedAssets;

        return $assets;
    }
}
