<?php

namespace App\Services\Studio;

use App\Models\Project;

interface StudioContract
{

    /**
     *  Studio constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project);

    /**
     *  Output presets
     *
     *  See: config/aws.php
     * @link https://docs.aws.amazon.com/elastictranscoder/latest/developerguide/working-with-presets.html?shortFooter=true
     * @param array $presets
     * @return mixed
     */
    public function setPresets(array $presets);

    /**
     *  Studio outputs which already exists
     * @return array
     */
    public function outputs();

    /**
     *  Run project output
     *
     * @return bool status
     */
    public function export();
}
