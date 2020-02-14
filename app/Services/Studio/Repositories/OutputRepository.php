<?php

namespace App\Services\Studio\Repositories;

use App\Models\Project;
use App\Services\Studio\Entities\Output;
use App\Services\Studio\Entities\OutputContract;
use App\Services\Studio\Entities\PresetContract;
use Illuminate\Support\Collection;

class OutputRepository implements OutputRepositoryContract
{

    /** @var \App\Models\Project $project */
    protected $project;

    /** @var \Illuminate\Support\Collection */
    protected $outputs;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->outputs = collect();
    }

    public function all(): Collection
    {
        return $this->outputs;
    }

    public function createFor(PresetContract $preset): OutputRepositoryContract
    {
        $this->outputs->push(Output::make($preset, $this->project));
        return $this;
    }

    public function find(string $key): OutputContract
    {
        return $this->outputs->first(function (OutputContract $output) use ($key) {
            return $output->getPreset()->getKey() == $key;
        });
    }
}
