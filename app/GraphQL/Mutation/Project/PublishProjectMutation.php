<?php

namespace App\GraphQL\Mutation\Project;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Jobs\FireProjectExport;
use App\Models\ProjectProcess;
use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class PublishProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'publishProject',
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

        // NOTE: Job id is not actual when you you use `sync` driver
        /** @var int $job_id */
        $job_id = dispatch_now(new FireProjectExport($project));

        // Set status WAITING for the project with job ID (Job ID is useful for canceling job in future)
        $project->processes()->create([
            'job_id' => $job_id,
            'status' => ProjectProcess::STATUS_WAITING
        ]);

        return $project->fresh();
    }
}
