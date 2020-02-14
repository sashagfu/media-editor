<?php

namespace App\Services\Studio\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface InputRepositoryContract
{

    /**
     *  Repository constructor
     * @param Collection $inputs
     */
    public function __construct(Collection $inputs);

    /**
     *  Get inputs for Elastic Transcoder
     * @return array
     */
    public function getInputsCompositions() : array;
}
