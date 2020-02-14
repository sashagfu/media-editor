<?php

namespace App\GraphQL\Mutation\Project;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\User;
use App\Models\Project;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Support\Facades\Storage;

class DeleteProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProject',
    ];

    public function type()
    {
        return GraphQL::type('Project');
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'projectId' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $project = Project::findOrFail($args['projectId']);

        $project->projectImages()->delete();

        $project->projectVideos()->delete();

        $project->projectAudio()->delete();

        $project->processes()->delete();

        $project->inputs()->delete();

        $project->delete();

        $uploadsDir = config('filesystems.disks.s3.uploads_path');

        Storage::disk('s3')->deleteDirectory($project->path . $uploadsDir);

        return $project;
    }
}
