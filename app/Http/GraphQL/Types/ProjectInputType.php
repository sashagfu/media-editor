<?php

namespace App\Http\GraphQL\Types;

use App\Models\ProjectInput;

class ProjectInputType
{
    public function projectId(ProjectInput $input)
    {
        return $input->project_id;
    }

    public function objectId(ProjectInput $input)
    {
        return $input->object_id;
    }

    public function layerId(ProjectInput $input)
    {
        return $input->layer_id;
    }

    public function startFrom(ProjectInput $input)
    {
        return $input->start_from * 1000;
    }

    public function volumeControls(ProjectInput $input)
    {
        return $input->volume_levels;
    }

    public function position(ProjectInput $input)
    {
        return $input->position * 1000;
    }

    public function length(ProjectInput $input)
    {
        return $input->length * 1000;
    }
}
