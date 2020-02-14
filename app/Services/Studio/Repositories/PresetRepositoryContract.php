<?php

namespace App\Services\Studio\Repositories;

use App\Services\Studio\StudioException;
use Illuminate\Support\Collection;
use App\Services\Studio\Entities\PresetContract;

interface PresetRepositoryContract
{

    /**
     * @param string $key
     * @return PresetContract
     * @throws StudioException
     */
    public function create(string $key) : PresetContract;

    /**
     *  Get preset
     * @param string $key
     * @return PresetContract
     */
    public function findOrCreate($key) : PresetContract;

    /**
     *  Get all presets
     * @return \Illuminate\Support\Collection|\App\Services\Studio\Entities\PresetContract[]
     */
    public function all() : Collection;
}
