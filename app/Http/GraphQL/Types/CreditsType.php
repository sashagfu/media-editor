<?php

namespace App\Http\GraphQL\Types;

use App\Models\Asset;
use App\Models\Project;
use App\Models\ProjectCredit;
use App\Models\User;

class CreditsType
{
    public function author($credit)
    {
        return User::find($credit['author']['id']);
    }

    // project that was clipped and used in this project
    public function foreignCreditsProject($credit)
    {
        return Project::find($credit['project']['id']);
    }

    public function deprecated($credit)
    {
        $project = Project::find($credit['project']['id']);

        return !!$project->assets()
                         ->where('version', '>', $credit['project']['version'])
                         ->get()
                         ->count();
    }
}
