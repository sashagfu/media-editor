<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class FetchMyFeed
{
    public function resolve($rootValue, array $args)
    {
        $user = Auth::user();

        $projects = Project::whereIn('author_id', $user->following->pluck('id'))
                          ->where('status', Project::STATUS_PUBLISHED)
                          ->get();

        return $projects;
    }
}
