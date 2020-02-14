<?php

namespace App\Services\Studio\Entities;

use App\Models\Asset;
use App\Models\Project;
use App\Services\Studio\Repositories\OutputRepository;
use Illuminate\Support\Str;

class Output implements OutputContract
{

    /** @var \App\Services\Studio\Entities\PresetContract $preset*/
    protected $preset;


    /** @var string $path */
    protected $path;

    public function __construct(PresetContract $preset, Project $project)
    {
        $this->preset = $preset;

        $project_slug = Str::slug($project->title);
        $date_mark = date('Y-m-d_H_i_s');

        $full_type = Asset::FULL_TYPE;

        $full_ext = Asset::VIDEO_EXT;
        
        $this->path = "{$project->path}assets/{$project->version}/{$full_type}.{$full_ext}";
    }

    /**
     * @param PresetContract $preset
     * @param Project $project
     * @return OutputContract
     */
    public static function make(PresetContract $preset, Project $project) : OutputContract
    {
        return new static($preset, $project);
    }

    /**
     * @param string $ext
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->path;
    }

    /**
     * @return PresetContract
     */
    public function getPreset(): PresetContract
    {
        return $this->preset;
    }
}
