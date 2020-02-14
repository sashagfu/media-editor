<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Services\Studio\Tools\Media;

/**
 * Class Input
 * @property int $layer_id
 * @property float $position
 * @property float $start_from
 * @property float $length
 * @property float $end_point
 * @property string $type
 */

class InputVideo extends BaseInput implements InputContract
{

    protected $is_visible = true;
    protected $is_frame = false;
    protected $has_alpha_channel = false;

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        } elseif (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif ($name == 'end_point') {
            return $this->attributes['position'] + $this->attributes['length'];
        } elseif ($name == 'layer_id') {
            return $this->model->layer_id;
        }

        return null;
    }

    /**
     *  Input data from Elastic Transcoder
     *
     * @link https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.ElasticTranscoder.ElasticTranscoderClient.html#_createJob
     * @return array
     */
    public function getCompositions() : array
    {
        return [ $this->createComposition() ];
    }


    /**
     *  Path to audio file (if its video)
     * @return string
     */
    public function getAudioChannel(): string
    {
        $tmp_file = Media::videoToAudio(
            $this->model->object->file_path,
            $this->start_from,
            $this->length,
            'wav',
            $this->model->project
        );

        if ($this->model->volume_levels) {
            return Media::renderVolumeLevels(
                $tmp_file,
                $this->model->volume_levels,
                $this->model->project
            );
        }

        return $tmp_file;
    }

    /**
     *  Path to input file on S3
     * @return string
     */
    public function getKey(): string
    {
        return $this->model->object->file_path_effected
            ? $this->model->object->s3_path_effected
            : $this->model->object->s3_path;
    }

    public function prepare(): bool
    {
        try {
            Media::addEffects($this->model);

            return true;
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }
}
