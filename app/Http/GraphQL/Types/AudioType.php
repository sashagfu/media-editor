<?php

namespace App\Http\GraphQL\Types;

use App\Models\Audio;

class AudioType
{
    public function waveformData(Audio $audio)
    {
        return $audio->sprite;
    }

    public function fileType()
    {
        return Audio::MORPH_TYPE;
    }

    public function filePath(Audio $audio)
    {
        return $audio->file_path;
    }

    public function projectId(Audio $audio)
    {
        return $audio->audioable_id;
    }
}
