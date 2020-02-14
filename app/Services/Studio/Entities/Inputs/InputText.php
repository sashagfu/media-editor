<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Models\ProjectInput;
use App\Models\Text;
use App\Services\Studio\Entities\Font;
use App\Services\Studio\StudioException;
use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;

class InputText extends BaseInput implements InputContract
{
    use Convertable;

    /**
     *  If video has background then there is not sense to create video with full input duration
     *  We just have to create small video and loop it to specified duration
     */
    const LOOPED_VIDEO_DURATION = 1.0;

    /** @var \App\Services\Studio\Entities\FontContract $font */
    protected $font;

    /** @var array $background RGBA array */
    protected $background = [0,0,0,1];

    /**
     * InputText constructor.
     * @param ProjectInput $model
     * @param float|null $position
     * @param float|null $length
     * @param float|null $star_from
     * @throws StudioException
     */
    public function __construct(
        ProjectInput $model,
        float $position = null,
        float $length = null,
        float $star_from = null
    ) {
        parent::__construct($model, $position, $length, $star_from);

        if ($model->object instanceof Text) {
            if (!$model->object->content) {
                $this->is_visible = false;
            } else {
                $this->font = Font::make($model->object->font);
                $this->font->setSize($model->object->size)
                    ->setColor($model->object->color)
                    ->setType($model->object->font_type);
                $this->background = $model->object->background;
            }
        } else {
            throw new StudioException(
                "Text settings is not defined",
                StudioException::TEXT_SETTINGS_IS_NOT_DEFINED
            );
        }
    }

    public function getKey(): string
    {
        return $this->isConverted() ? $this->getConvertedVideo() : $this->convertToVideo();
    }

    public function getCompositions(): array
    {
        if ($this->hasAlphaChannel()) {
            return [ $this->createComposition($this->position, '0.0') ];
        }

        $compositions = [];

        $left = $this->length;
        $position = $this->position;
        while ($left > 0) {
            $length = $left > self::LOOPED_VIDEO_DURATION ? self::LOOPED_VIDEO_DURATION : $left;
            $compositions[] = $this->createComposition($position, '0.0', $length);
            $left -= $length;
            $position += $length;
        }

        return $compositions;
    }

    /**
     *  Path to audio file (if its video)
     * @return string
     */
    public function getAudioChannel() : string
    {
        return Media::silence($this->length, $this->model->project);
    }

    protected function convertToVideo()
    {
        if ($this->hasAlphaChannel() || $this->isFrame()) {
            // TODO: Add overlaying support
            return null;
        }
        $image = Media::textToImage(
            $this->font,
            $this->model->object->content,
            $this->model->transform['position']['x'] ?? 0,
            $this->model->transform['position']['y'] ?? 0,
            1024,
            720,
            $this->background
        );

        $converted_video = "{$this->model->project->path}image_input{$this->model->id}-converted.mp4";

        $tmp_video = Media::imageToVideo($image, self::LOOPED_VIDEO_DURATION);

        Storage::disk('s3')->put($converted_video, file_get_contents($tmp_video));

        $this->model->update(['converted_file' => $converted_video]);

        return $converted_video;
    }

    protected function getConvertedFileLength(): float
    {
        return $this->has_alpha_channel ? $this->length : self::LOOPED_VIDEO_DURATION;
    }
}
