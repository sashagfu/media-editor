<?php

namespace App\Http\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchProjectMedia
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        $project = $user->projects->where('id', $args['projectId'])->first();

        if (!$project) {
            throw new \Exception(trans('projects.wrong_access_rights'));
        }

        return $project->projectVideos
            ->concat($project->projectAudio)
            ->concat($project->projectImages);
    }
}
