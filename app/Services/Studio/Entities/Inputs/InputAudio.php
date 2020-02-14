<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Services\Studio\Tools\Media;

class InputAudio extends BaseInput implements InputContract
{

    public $is_audio = true;

    /** @var bool $is_visible Audio input has no video channel */
    protected $is_visible = false;


    public function getKey(): string
    {
        return null;
    }

    /**
     *  Channel of audio is input file
     * @return string
     */
    public function getAudioChannel(): string
    {
        if ($this->model->volume_levels) {
            return Media::renderVolumeLevels(
                $this->model->object->file_path,
                $this->model->volume_levels,
                $this->model->project
            );
        }

        return $this->model->object->file_path;
    }

    /**
     *  Get composition for Elastic Transcoder
     *  (Elastic Transcoder doesn't support audio processing)
     * @return array
     */
    public function getCompositions(): array
    {
        return [];
    }
}
