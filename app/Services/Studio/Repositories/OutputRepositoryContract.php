<?php

namespace App\Services\Studio\Repositories;

use App\Services\Studio\Entities\OutputContract;
use App\Services\Studio\Entities\PresetContract;
use Illuminate\Support\Collection;
use App\Models\Project;

interface OutputRepositoryContract
{

    /**
     * Repository constructor
     *
     * @param Project $project
     */
    public function __construct(Project $project);

    /**
     *  Get all outputs
     *
     * @return Collection
     */
    public function all() : Collection;

    /**
     * Add output
     *
     * @param \App\Services\Studio\Entities\PresetContract
     * @return OutputRepositoryContract
     */
    public function createFor(PresetContract $preset) : OutputRepositoryContract;

    /**
     *  Find output by preset key
     *
     * @param string $key
     * @return OutputContract
     */
    public function find(string $key) : OutputContract;
}
