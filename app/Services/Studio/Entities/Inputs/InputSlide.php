<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Services\Studio\Entities\Font;
use App\Services\Studio\StudioException;
use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class InputSlide extends BaseInput implements InputContract
{
    use Convertable;

    /**
     *  Length of video that's created from image
     */
    const IMAGE_VIDEO_LENGTH = 1.0;

    protected $is_visible = true;
    protected $is_frame = false;
    protected $has_alpha_channel = true;

    /**
     *  S3 file path
     *
     * @return string
     */
    public function getKey(): string
    {
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
        return [];
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
        // TODO: Implement getConvertedFileLength() method.
    }

    public static function renderOneSlide(
        $slide,
        $output,
        $tmp_with_slides,
        $tmp_with_slides_name
    ) {
        $tmp_slide = self::slideToImage($slide);

        $slide_filters = sprintf(
            "[1:v]fade=t=in:st=%s:d=%s,fade=t=out:st=%s:d=%s[over]",
            $slide->position,
            $slide->object->effects['fadeIn']['duration'] ?? 0.01,
            $slide->position + $slide->length - $slide->object->effects['fadeOut']['duration'] ?? 0.01,
            $slide->object->effects['fadeOut']['duration'] ?? 0.01
        );

        $video_filters = sprintf(
            "[0:v][over]overlay=0:0:enable='between(t,%s,%s)'",
            $slide->position, // start slide time
            $slide->position + $slide->length // end slide time
        );

        // Render slide in video
        $render_slide_cmd = sprintf(
            "%s -y -i %s -loop 1 -i %s -shortest -filter_complex \"%s;%s\" -pix_fmt yuv420p -c:a copy %s",
            env('FFMPEG'),
            (Storage::disk('uploads')->exists('/tmp/project_'. $slide->project_id . '/' . $tmp_with_slides_name)) ?
                $tmp_with_slides :
                Storage::disk('s3')->url($output->getFilePath()),
            $tmp_slide,
            $slide_filters,
            $video_filters,
            $tmp_with_slides
        );

        try {
            $render_slide_process = new Process($render_slide_cmd);
            $render_slide_process->setTimeout(0);
            $render_slide_process->run();

            $remove_slide = new Process("rm {$tmp_slide}");
            $remove_slide->run();
        } catch (\Exception $e) {
            logger($e->getMessage());
            throw new StudioException(
                'Slide has not been created',
                StudioException::SLIDE_HAS_NOT_BEEN_RENDERED,
                $e
            );
        }
    }

    public static function slideToImage($slide)
    {
        // resolution coefficient on front (640x360) to calculate relative position on different output resolutions
        // TODO: Change coefficient to different output resolutions
        $k = 1280 / 640;
        try {
            $images = [];
            $slide->object
                ->texts()
                ->each(
                    function ($text) use ($slide, &$images, $k) {
                        $font = Font::make($text->font);
                        $font->setSize($text->size * $k)
                             ->setColor($text->color)
                             ->setType($text->font_type);
                        $background = $text->background;

                        // ($text->box_size['w'] / 2) --- divide width and height on 2,
                        // due position detecting difference on back and front (from center and left top corner)
                        $positionX = ($text->position['x'] - ($text->box_size['w'] / 2)) * $k ?? 0;
                        $positionY = ($text->position['y'] - ($text->box_size['h'] / 2)) * $k ?? 0;

                        $images[] = Media::textToImage(
                            $font,
                            $text->content,
                            $positionX,
                            $positionY,
                            1280,
                            720,
                            $background
                        );
                    }
                );

            // If slide has only one text return already rendered image, else join images
            if (count($images) < 2) {
                return collect($images)->first();
            }

            $inputs = " ";

            $tmp_path = config('filesystems.disks.uploads.root')
                        . config('filesystems.disks.uploads.tmp_path');

            $tmp_slide = $tmp_path . md5(time()) . '.png';

            foreach ($images as $image) {
                $inputs = $inputs . " -i {$image} ";
            }

            $join_images_cmd = sprintf(
                "%s %s -filter_complex \"overlay\" %s ",
                env('FFMPEG'),
                $inputs,
                $tmp_slide
            );

            $join_images_process = new Process($join_images_cmd);
            $join_images_process->setTimeout(0);
            $join_images_process->run();

            // Remove temp images
            $tmp_images = "";
            foreach ($images as $image) {
                $tmp_images = $tmp_images . " {$image} ";
            }

            $remove_tmp_images_process = new Process("rm {$tmp_images}");
            $remove_tmp_images_process->setTimeout(0);
            $remove_tmp_images_process->run();

            if ($join_images_process->isSuccessful()) {
                return $tmp_slide;
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            throw new StudioException(
                'Texts has not been joined',
                StudioException::TEXTS_HAS_NOT_BEEN_JOINED,
                $e
            );
        }
    }
}
