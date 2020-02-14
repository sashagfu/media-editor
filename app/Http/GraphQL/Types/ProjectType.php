<?php

namespace App\Http\GraphQL\Types;

use App\Models\Project;
use App\Models\ProjectCredit;
use App\Models\User;

class ProjectType
{
    public function assets(Project $project, $args)
    {
        if (!isset($args['version']) || !$args['version']) {
            $args['version'] = $project->version;
        }

        return $project->assets()->where('version', $args['version'])->get();
    }

    public function value(Project $project)
    {
        return $project->inputs;
    }

    public function thumbPath(Project $project)
    {
        return $project->thumb_path;
    }

    public function spritePath(Project $project)
    {
        if ($project->status == Project::STATUS_PROCESSING) {
            return $project->assets
                ->where('version', $project->version - 1)
                ->where('type', 'FULL')
                ->first()
                ->spritePath ?? null;
        }
        return $project->assets->where('version', $project->version)
            ->where('type', 'FULL')
            ->first()
            ->spritePath ?? null;
    }
}
