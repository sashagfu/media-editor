<?php

namespace App\Services\Studio\Repositories;

use App\Models\ProjectInput;
use App\Services\Studio\Entities\Inputs\InputContract;
use App\Services\Studio\Entities\Inputs\BlankInput;
use App\Services\Studio\Entities\Inputs\InputAudio;
use App\Services\Studio\Entities\Inputs\InputImage;
use App\Services\Studio\Entities\Inputs\InputSlide;
use App\Services\Studio\Entities\Inputs\InputText;
use App\Services\Studio\Entities\Inputs\InputVideo;
use App\Services\Studio\StudioException;
use Illuminate\Database\Eloquent\Collection;

class InputRepository implements InputRepositoryContract
{

    /**
     *  Studio inputs
     * @var \Illuminate\Support\Collection|\App\Services\Studio\Entities\Inputs\InputContract[] $inputs
     */
    protected $inputs;

    public function __construct(Collection $models)
    {
        /** @var float $project_duration */
        $project_duration = $models->max(function (ProjectInput $model) {
            return $model->end_point;
        });

        // Add black screen video for covering all project
        $this->inputs = collect()->push(new BlankInput(0.0, $project_duration));

        $models->each(function (ProjectInput $model) {
            $this->addModelInputs($model);
        });
    }

    /**
     *  Get all inputs
     *
     * @return InputContract[]|\Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->inputs;
    }

    /**
     *  Get all inputs for Elastic
     *
     * @return array
     */
    public function getInputsCompositions(): array
    {
        return $this->inputs
            ->each(function (InputContract $input) {
                // Make additional operations with input
                $input->prepare();
            })
            ->filter(function (InputContract $input) {
                return !$input->getModel()->isAudio() &&
                       !$input->getModel()->isSlide() &&
                       (float)$input->length > 0.001;
            })
            ->map(function (InputContract $input) {
                return $input->getCompositions();
            })
            ->flatten(1)
            ->sortBy('position')
            ->without('position')
            ->values()
            ->toArray();
    }

    /**
     *  Add inputs to repository from model
     *
     * @param ProjectInput $model
     * @return $this
     * @throws \App\Services\Studio\StudioException
     */
    protected function addModelInputs(ProjectInput $model)
    {
        if ($model->isAudio()) {
            $this->addInput($model);
        } else {
            /** @var \Illuminate\Support\Collection|\App\Services\Studio\Entities\Inputs\InputContract[] $overlaps */
            $overlaps = $this->inputs
                ->filter(function (InputContract $input) use ($model) {
                    if (!$input->is_audio && !$input->hasAlphaChannel()) {
                        if ($input->coversCompletely($model->layer_id, $model->position, $model->end_point)) {
                            return true;
                        } elseif ($input->covers($model->layer_id, $model->position, $model->end_point)) {
                            return true;
                        }
                    }
                    return false;
                })
                ->sortBy('position');

            if ($overlaps->isNotEmpty()) {
                /**
                 * @var int $index
                 * @var \App\Models\ProjectInput $overlap
                 */
                foreach ($overlaps as $index => $overlap) {
                    // Start
                    if ($overlap->position <= $model->position
                        && $overlap->end_point > $model->position
                        && $overlap->end_point < $model->end_point
                    ) {
                        $model->length = $model->length - ($overlap->end_point - $model->position);
                        $model->start_from = $model->start_from + ($overlap->end_point - $model->position);
                        $model->position = $overlap->end_point;

                        // End
                    } elseif ($overlap->position < $model->end_point
                        && $overlap->position > $model->position
                        && $model->end_point <= $overlap->end_point
                    ) {
                        $model->length = $model->length - ($model->end_point - $overlap->position);

                        // Middle
                    } elseif ($overlap->position > $model->position && $overlap->end_point < $model->end_point) {
                        $duration = $overlap->position - $model->position;

                        $this->addInput($model, $model->position, $model->start_from, $duration);

                        $model->position = $overlap->end_point;
                        $model->start_from = $model->start_from + $duration + $overlap->length;
                        $model->length = $model->length - ($duration + $overlap->length);
                    }

                    if (!isset($overlaps[$index + 1])) {
                        $this->addInput($model, $model->position, $model->start_from, $model->length);
                    }
                }
            } else {
                $this->addInput($model);
            }
        }

        return $this;
    }

    /**
     *  Create one input
     *
     * @param ProjectInput $model
     * @param null $position
     * @param null $start_from
     * @param null $length
     * @return $this
     * @throws \App\Services\Studio\StudioException
     */
    protected function addInput(ProjectInput $model, $position = null, $start_from = null, $length = null)
    {
        if ($model->isAudio()) {
            $input = new InputAudio($model, $position, $length, $start_from);
        } elseif ($model->isImage()) {
            $input = new InputImage($model, $position, $length, $start_from);
        } elseif ($model->isText()) {
            $input = new InputText($model, $position, $length);
        } elseif ($model->isSlide()) {
            $input = new InputSlide($model, $position, $length);
        } elseif ($model->isVideo()) {
            $input = new InputVideo($model, $position, $length, $start_from);
        } else {
            throw new StudioException("Input type id invalid");
        }

        $this->beforePush($input);
        $this->inputs->push($input);

        // Resort
        $this->inputs = $this->inputs
            ->sortBy('position')
            ->sortByDesc(function (InputContract $input) {
                return $input->layer_id;
            });

        return $this;
    }

    /**
     *  Correct existed inputs if pushed input covers them
     *
     * @param InputContract $pushedInput
     */
    protected function beforePush(InputContract $pushedInput)
    {
        if ($this->inputs->isNotEmpty() && !$pushedInput->is_audio && !$pushedInput->hasAlphaChannel()) {
            $splitInputs = collect();
            $this->inputs = $this->inputs->map(function (InputContract $input) use ($pushedInput, $splitInputs) {
                if ($input->is_audio || $input->hasAlphaChannel()) {
                    // Pushing input can't cover audio or input with alpha channel (transparency)
                    return $input;
                } elseif ($pushedInput->coversCompletely($input->layer_id, $input->position, $input->end_point)) {
                    // If pushing input is covers completely another
                    // Delete covered input
                    return null;
                } elseif ($pushedInput->covers($input->layer_id, $input->position, $input->end_point)) {
                    $position = $input->position;
                    $start_from = $input->start_from;
                    $length = $input->length;

                    // Covers at the beginning
                    if ($pushedInput->position <= $input->position
                        && $pushedInput->end_point > $input->position
                        && $pushedInput->end_point < $input->end_point
                    ) {
                        $cutDuration = $pushedInput->end_point - $input->position;
                        $position = $pushedInput->end_point;
                        $start_from = $input->start_from + $cutDuration;
                        $length = $input->length - $cutDuration;

                        // Covers at the end
                    } elseif ($pushedInput->position > $input->position
                        && $pushedInput->position < $input->end_point
                        && $pushedInput->end_point >= $input->end_point
                    ) {
                        $length = $input->length - ($input->end_point - $pushedInput->position);

                        // Covers in the middle
                    } elseif ($pushedInput->position >= $input->position
                        && $pushedInput->position < $input->end_point
                        && $pushedInput->end_point < $input->end_point
                    ) {
                        $length -= ($pushedInput->length + ($input->end_point - $pushedInput->end_point));

                        /** @var float $cutDuration Duration that was removed from input */
                        $cutDuration = $pushedInput->length + ($pushedInput->position - $input->position);

                        if ($input->length - $cutDuration > 0) {
                            $splitInputs->push((clone $input)->correct([
                                'position' => $pushedInput->end_point,
                                'start_from' => $input->start_from + $cutDuration,
                                'length' => $input->length - $cutDuration
                            ]));
                        }
                    }

                    if ($length <= 0) {
                        return null;
                    }
                    return $input->correct(compact('position', 'start_from', 'length'));
                }
                return $input;
            })
                ->filter(function ($input) {
                    return !is_null($input);
                });

            if ($splitInputs->isNotEmpty()) {
                $this->inputs = $this->inputs->merge($splitInputs);
            }
        }
    }
}
