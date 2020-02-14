<?php

namespace App\Services\Studio;

class StudioException extends \Exception
{

    // General exceptions codes
    const PROCESS_EXCEPTION = 10;
    const ELASTIC_JOB_EXCEPTION = 11;

    // Preset exceptions codes
    const PRESET_VALIDATION_ERROR = 20;
    const PRESET_IS_NOT_DEFINED = 21;
    const PRESET_CANNOT_BE_FETCHED = 22;

    // Audio processing exceptions codes
    const SOUND_HAS_NOT_BEEN_REPLACED = 30;
    const LAYER_SOUND_HAS_NOT_BEEN_CREATED = 31;

    // Text processing exceptions codes
    const TEXT_SETTINGS_IS_NOT_DEFINED = 40;
    const TEXTS_HAS_NOT_BEEN_JOINED = 41;

    // Thumbs processing exception codes
    const THUMBS_HAS_NOT_BEEN_CREATED = 50;

    // Fonts exceptions codes
    const FONTS_DIRECTORY_IS_INVALID = 60;
    const FONT_NOT_FOUND = 61;
    const INCORRECT_FONT_SIZE = 62;
    const INCORRECT_FONT_TYPE = 63;
    const INCORRECT_FONT_COLOR = 64;

    // Slides exception codes
    const SLIDE_HAS_NOT_BEEN_RENDERED = 70;

    /** @var mixed $data Exception details */
    protected $data;

    /**
     *  Set exception details
     *
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
