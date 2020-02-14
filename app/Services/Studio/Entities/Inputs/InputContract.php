<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Models\ProjectInput;

/**
 * Interface InputContract
 * @property int $layer_id
 * @property float $position
 * @property float $start_from
 * @property float $end_point
 * @property float $length
 * @property float $is_video
 * @property float $is_image
 * @property float $is_audio
 */

interface InputContract
{

    /**
     *  Correct input data
     *
     * @param array $data
     * @return InputContract
     */
    public function correct(array $data) : InputContract;

    /**
     *  Input data from Elastic Transcoder
     *
     * @link https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.ElasticTranscoder.ElasticTranscoderClient.html#_createJob
     * @return array
     */
    public function getCompositions() : array;

    /**
     * @return ProjectInput
     */
    public function getModel() : ProjectInput;

    /**
     *  Path to audio file (if its video)
     * @return string
     */
    public function getAudioChannel() : string;

    /**
     *  Path to input file on S3
     * @return string
     */
    public function getKey() : string;

    /**
     *  Check if input covers the other
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function covers(int $layer_id, float $position, float $end_point) : bool;

    /**
     *  Check if input is frame.
     *
     *  !! Frame is an input that has alpha channel and has no background
     *
     * @return bool
     */
    public function isFrame() : bool;

    /**
     *  Check if input has alpha channel
     *
     * @return bool
     */
    public function hasAlphaChannel() : bool;

    /**
     *  Check if input covers completely other
     *
     * @param int $layer_id
     * @param float $position
     * @param float $end_point
     * @return bool
     */
    public function coversCompletely(int $layer_id, float $position, float $end_point) : bool;


    /**
     *  Prepare input on s3 before send to transcoder
     *
     * @return bool
     */
    public function prepare() : bool;
}
