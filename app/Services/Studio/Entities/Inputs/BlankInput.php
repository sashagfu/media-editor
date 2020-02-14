<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Models\ProjectInput;
use App\Services\Studio\Studio;
use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;

class BlankInput extends BaseInput implements InputContract
{

    /**
     *  Length of video that's created for filling empty spaces
     */
    const BLANK_VIDEO_LENGTH = 1.0;
    const BLANK_VIDEO_NAME = 'BLANK_SCREEN.mp4';

    protected $is_visible = true;
    protected $is_frame = false;
    protected $has_alpha_channel = false;

    protected $attributes = [
        'layer_id' => -1,
        'position' => 0.0,
        'length' => 0.0,
    ];

    public function __construct(float $position, float $length)
    {
        parent::__construct(new ProjectInput(), $position, $length);
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } elseif ($name === 'end_point') {
            return $this->attributes['position'] + $this->attributes['length'];
        }

        return null;
    }

    /**
     *  Input data from Elastic Transcoder
     *
     * @link https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.ElasticTranscoder.ElasticTranscoderClient.html#_createJob
     * @return array|null
     */
    public function getCompositions(): array
    {
        $inputs = [];
        $length = $this->length;
        $position = $this->position;
        while ($length > 0) {
            $duration = $length > self::BLANK_VIDEO_LENGTH ? self::BLANK_VIDEO_LENGTH : $length;
            $inputs[] = [
                'Key' => $this->getKey(),
                'position' => $position,
                'TimeSpan' => [
                    'StartTime' => "0.0",
                    'Duration' => (string)round($duration, Studio::ROUND_PLACES),
                ]
            ];

            $length -= $duration;
            $position += $duration;
        }

        return $inputs;
    }

    /**
     *  Path to audio file (if its video)
     * @return string
     */
    public function getAudioChannel() : string
    {
        return Media::silence($this->length);
    }

    /**
     *  Path to input file on S3
     * @return string
     */
    public function getKey() : string
    {
        if (!Storage::disk('s3')->exists(self::BLANK_VIDEO_NAME)) {
            Media::createBlackScreenVideo('s3', self::BLANK_VIDEO_NAME, self::BLANK_VIDEO_LENGTH);
        }

        return self::BLANK_VIDEO_NAME;
    }

    /**
     *  Blank input cannot cover other inputs
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function covers(int $layer_id, float $position, float $end_point): bool
    {
        return false;
    }

    /**
     *  Blank input cannot cover other inputs
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function coversCompletely(int $layer_id, float $position, float $end_point): bool
    {
        return false;
    }
}
