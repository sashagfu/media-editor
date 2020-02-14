<?php

namespace App\GraphQL\Mutation\Project;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AddProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addProject',
    ];

    public function type()
    {
        return GraphQL::type('Project');
    }

    public function args()
    {
        return [
            'project' => ['name' => 'project', 'type' => GraphQL::type('ProjectInput')],
        ];
    }

    public function rules()
    {
        return [
            'project' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $project_data = collect($args['project'])
            ->only(
                [
                    'title',
                    'description'
                ]
            )
            ->toArray();
        $project_data['status'] = Project::STATUS_DRAFT;
        $project_data['author_id'] = AuthHelper::myId();

        $project = Project::create($project_data);

        $tags = (isset($args['project']['tags'])) ? $args['project']['tags'] : null;

        if ($tags) {
            foreach ($tags as $new_tag) {
                $tag = Tag::where('name', $new_tag['name'])->first();

                if (!$tag) { // if tag doesn't exists
                    $tag = Tag::create([
                        'name' => $new_tag['name'],
                    ]);
                }

                $project->tags()->attach($tag->id);
            }
        }

        return $project;
    }
}
