<?php
/**
 * Created by PhpStorm.
 * User: davydgoloviy
 * Date: 3/12/18
 * Time: 21:58
 */

namespace App\Services\Studio\Entities;

use App\Models\Project;

interface OutputContract
{

    /**
     *  Create output for preset
     *
     * @param PresetContract $preset
     * @param Project $project
     * @return OutputContract
     */
    public static function make(PresetContract $preset, Project $project) : OutputContract;

    /**
     *  Get file path
     *
     * @param string $ext
     * @return string
     */
    public function getFilePath() : string;

    /**
     *  Get preset
     *
     * @return PresetContract
     */
    public function getPreset() : PresetContract;
}
