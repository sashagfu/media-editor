<?php

namespace App\Services\Studio\Repositories;

use Illuminate\Support\Collection;
use App\Services\Studio\Entities\FontContract;

interface FontRepositoryContract
{

    /**
     *  Get all fonts
     *
     * @return Collection
     */
    public function all() : Collection;

    /**
     * @param string $key
     * @return \App\Services\Studio\Entities\FontContract
     */
    public function find(string $key) : FontContract;
}
