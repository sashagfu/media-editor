<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Models\ProjectInput;

abstract class BaseInput implements InputContract
{
    /** @var bool $has_alpha_channel Input with opacity or it's text */
    protected $has_alpha_channel = false;

    /** @var bool $is_frame Input is scaled or cropped */
    protected $is_frame = false;

    /** @var bool $is_visible Video, image and text is visible */
    protected $is_visible = true;

    /**
     * @var array $attributes
     */
    protected $attributes = [
        'position' => 0.0,
        'start_from' => 0.0,
        'length' => 0.0,
    ];

    /**
     * @var \App\Models\ProjectInput $model
     */
    protected $model;

    public function __construct(
        ProjectInput $model,
        float $position = null,
        float $length = null,
        float $star_from = null
    ) {
        $this->model = $model;
        $this->attributes['position'] = $position ?? $model->position;
        $this->attributes['start_from'] = $star_from ?? $model->start_from;
        $this->attributes['length'] = $length ?? $model->length;
    }

    // GETTERS

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif ($name === 'end_point') {
            return $this->attributes['position'] + $this->attributes['length'];
        } elseif ($name === 'layer_id') {
            return $this->model->layer_id;
        } elseif ($name === 'type') {
            return $this->getModel()->type;
        }

        return null;
    }

    /**
     * @return ProjectInput
     */
    public function getModel() : ProjectInput
    {
        return $this->model;
    }

    // TRIGGERS

    /**
     *  Method is not available for now. (Future function)
     *
     * @return bool
     */
    public function isFrame(): bool
    {
        return $this->is_frame;
    }

    /**
     *  Method is not available for now. (Future function)
     *
     * @return bool
     */
    public function hasAlphaChannel(): bool
    {
        return $this->has_alpha_channel;
    }

    // TOOLS

    /**
     *  Correct input data
     *
     * @param array $data
     * @return InputContract
     */
    public function correct(array $data) : InputContract
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     *  Check if input covers the other
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function covers(int $layer_id, float $position, float $end_point) : bool
    {
        if (!$this->isFrame() && !$this->hasAlphaChannel()) {
            // Rule 1. An audio input isn't visible that's why it can't cover other inputs
            // Rule 2. Layer with greater value covers the layers with smaller
            if ($this->layer_id > $layer_id) {
                // Covers at the beginning
                if ($this->position <= $position && $this->end_point > $position && $this->end_point < $end_point) {
                    return true;
                    // Covers at the end
                } elseif ($this->position > $position && $this->position < $end_point && $this->end_point >= $end_point) { // @codingStandardsIgnoreEnd
                    return true;
                    // Is over in the middle
                } elseif ($this->position >= $position && $this->end_point <= $end_point) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *  Check if input covers the other
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function coversCompletely(int $layer_id, float $position, float $end_point) : bool
    {
        if (!$this->is_audio && !$this->hasAlphaChannel()) {
            // Rule 1. An audio input isn't visible that's why it can't cover other inputs
            // Rule 2. Layer with greater value covers the layers with smaller
            if ($this->layer_id > $layer_id && $this->position <= $position && $this->end_point >= $end_point) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Create composition entry
     *
     * @param float|null $position
     * @param float|null $start_time
     * @param float|null $duration
     * @return array
     */
    protected function createComposition(
        float $position = null,
        float $start_time = null,
        float $duration = null
    ) : array {
        return [
            'Key' => $this->getKey(),
            'position' => $position ?? $this->position,
            'TimeSpan' => [
                'StartTime' => (string)($start_time ?? $this->start_from),
                'Duration' => (string)($duration ?? $this->length)
            ]];
    }

    public function prepare(): bool
    {
        return false;
    }
}
