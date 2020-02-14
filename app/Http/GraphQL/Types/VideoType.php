<?php

namespace App\Http\GraphQL\Types;

use App\Models\Video;

class VideoType
{
    public function waveformData(Video $video)
    {
        return $video->waveform;
    }

    public function fileType()
    {
        return Video::MORPH_TYPE;
    }

    public function authorId(Video $video)
    {
        return $video->author_id;
    }

    public function thumbPath(Video $video)
    {
        return $video->thumbnail_path;
    }

    public function filePath(Video $video)
    {
        return $video->file_path;
    }

    public function spritePath(Video $video)
    {
        return $video->sprite_path;
    }

    public function projectId(Video $video)
    {
        return $video->videoable_id;
    }
}
