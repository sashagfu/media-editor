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

class EditProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'editProject',
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
        $project = Project::findOrFail($args['project']['id']);

        $updated_project = collect($args['project'])
            ->only(
                [
                    'title',
                    'description'
                ]
            )
            ->toArray();

        $project->update($updated_project);

        $tags = (isset($args['project']['tags'])) ? collect($args['project']['tags']) : null;

        $old_tags = $project->tags;
        $new_tags = [];
        $existed_tags = [];

        foreach ($tags as $tag) {
            if (isset($tag['id'])) {
                $existed_tag = Tag::findOrFail($tag['id']);
            } else {
                $existed_tag = Tag::where('name', $tag['name'])->first();
            }

            if ($existed_tag) { // check if tag already exists
                $existed_tags[] = $existed_tag;
            } else {
                $new_tag = Tag::create([
                    'name' => $tag['name'],
                ]);
                $new_tags[] = $new_tag;
            }
        }

        $tags = collect(array_merge($existed_tags, $new_tags));

        $deleted_tags = $old_tags->diff($tags); // get tags which was deleted from project

        $tags = $tags->pluck('id')
            ->toArray();

        $project->tags()->sync($tags);

        $project = $project->fresh();

        foreach ($deleted_tags as $tag) {  // remove unnecessary tags
            if ($tag->projects->count() <= 1) {
                $tag->delete();
            }
        }

        return $project;
    }
}
