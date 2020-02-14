<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;

class InputImage extends BaseInput implements InputContract
{
    use Convertable;

    /**
     *  Length of video that's created from image
     */
    const IMAGE_VIDEO_LENGTH = 1.0;

    protected $is_visible = true;
    protected $is_frame = false;
    protected $has_alpha_channel = false;

    /**
     *  S3 file path
     *
     * @return string
     */
    public function getKey(): string
    {
        // Elastic Transcoder is able to work only with video
        // That's why we need to convert image to video

        // If image is already converted then `converted_file` contain path to converted file on S3
        if ($this->isConverted()) {
            return $this->getConvertedVideo();
        }

        $converted_video = "{$this->model->project->path}image_input{$this->model->id}-converted.mp4";

        $tmp_video = Media::imageToVideo(
            $this->model->file->file_path,
            self::IMAGE_VIDEO_LENGTH,
            $this->model->project
        );

        Storage::disk('s3')->put($converted_video, file_get_contents($tmp_video));

        $this->model->update(['converted_file' => $converted_video]);

        return $converted_video;
    }

    /**
     *  Input data from Elastic Transcoder
     *
     * @link https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.ElasticTranscoder.ElasticTranscoderClient.html#_createJob
     * @return array
     */
    public function getCompositions() : array
    {

        /** @var array $compositions */
        $compositions = [];

        $length = $this->length;
        $position = $this->position;
        while ($length > 0) {
            // Duration of input cannot be longer than
            // converted image [into video] file duration (IMAGE_VIDEO_LENGTH)
            $duration = $length > self::IMAGE_VIDEO_LENGTH ? self::IMAGE_VIDEO_LENGTH : $length;

            // Add one item
            $compositions[] = $this->createComposition($position, 0.0, $duration);

            $length -= $duration;
            $position += $duration;
        }

        return $compositions;
    }

    /**
     *  Path to audio file (if its video)
     * @return string
     */
    public function getAudioChannel(): string
    {
        return Media::silence($this->length, $this->model->project);
    }

    protected function getConvertedFileLength(): float
    {
        return self::IMAGE_VIDEO_LENGTH;
    }
}
