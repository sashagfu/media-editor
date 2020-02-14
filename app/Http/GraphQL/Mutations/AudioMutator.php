<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Models\Audio;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AudioMutator
{
    public function delete($root, $args)
    {
        $audio_id = $args['id'];
        $project_id = $args['projectId'];
        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $project_id . '/uploads';
        $count = 1; // variable to set replace count

        $audio = Audio::findOrFail($audio_id);
        $audio_dir = $base_dir . '/audios/';
        $file_info = pathinfo($audio->file_path);
        $file_name = str_replace('source-', '', $file_info['filename'], $count);
        $ext = '.' . $file_info['extension'];

        $file_path = $audio_dir . 'source-' . $file_name . $ext;
        $sprite_path = $audio_dir . 'sprite-' . $file_name . '.json';

        $audio->delete();

        Storage::disk('s3')->delete($file_path);
        Storage::disk('s3')->delete($sprite_path);

        return $audio;
    }
}
