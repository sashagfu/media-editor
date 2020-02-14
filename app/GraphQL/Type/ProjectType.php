<?php

namespace App\GraphQL\Type;

use App\Models\Asset;
use App\Models\Project;
use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class ProjectType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Project',
        'description' => 'A project',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The id of the project',
            ],
            'authorId' => [
                'type'        => Type::int(),
                'description' => 'The id of the author of project',
                'resolve' => function ($project) {
                    return $project->author_id;
                }
            ],
            'author' => [
                'type'        => GraphQL::type('User'),
                'description' => 'The author of the project',
            ],
            'title' => [
                'type'        => Type::string(),
                'description' => 'The title of the project',
            ],
            'description' => [
                'type'        => Type::string(),
                'description' => 'The description of the project',
            ],
            'isDraft' => [
                'type'        => Type::boolean(),
                'description' => 'If project is draft',
                'resolve'     => function ($project) {
                    return $project->status == Project::STATUS_DRAFT;
                },
            ],
            'isProcessing' => [
                'type'        => Type::boolean(),
                'description' => 'If project is in processing',
                'resolve'     => function ($project) {
                    return $project->status == Project::STATUS_PROCESSING;
                },
            ],
            'isPublished' => [
                'type'        => Type::boolean(),
                'description' => 'If status is published',
                'resolve'     => function ($project) {
                    return $project->status == Project::STATUS_PUBLISHED;
                },
            ],
            'isFailed' => [
                'type'        => Type::int(),
                'description' => 'If status is failed',
                'resolve'     => function ($project) {
                    return $project->status == Project::STATUS_FAILED;
                },
            ],
            'progress' => [
                'type'        => Type::int(),
                'description' => 'The publish progress',
            ],
            'assets' => [
                'type'        => Type::listOf(GraphQL::type('Asset')),
                'description' => 'List of project assets',
                'args'        => (new AssetType())->fields(),
            ],
            'tags' => [
                'type'        => Type::listOf(GraphQL::type('Tag')),
                'description' => 'List of the tags',
            ],
            'comments' => [
                'type'        => Type::listOf(GraphQL::type('Comment')),
                'description' => 'List of the comments',
            ],
            'stars' => [
                'type'        => Type::listOf(GraphQL::type('User')),
                'description' => 'List of users starred project',
            ],
            'likes' => [
                'type'        => Type::listOf(GraphQL::type('User')),
                'description' => 'List of users liked project',
            ],
            'slides' => [
                'type' => Type::listOf(GraphQL::type('Slide')),
                'description' => 'List of slides related to project',
            ],
            'texts' => [
                'type' => Type::listOf(GraphQL::type('Text')),
                'description' => 'List of texts related to project',
            ],
            'userReaction' => [
                'type'        => Type::boolean(),
                'description' => 'If project is starred or liked by user',
            ],
            'isPerformance' => [
                'type'        => Type::boolean(),
                'description' => 'Is prjoject is performance',
            ],
            'clipsCount' => [
                'type'        => Type::int(),
                'description' => 'Count of clips',
            ],
        ];
    }

    public function resolveAssetsField(Project $project, $args)
    {
        if (!isset($args['version']) || !$args['version']) {
            $args['version'] = $project->version;
        }

        return $project->assets()->where($args)->get();
    }
}
