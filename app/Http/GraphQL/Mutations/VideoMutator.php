<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Models\Video;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VideoMutator
{
    public function delete($root, $args)
    {
        $video_id = $args['id'];
        $project_id = $args['projectId'];
        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $project_id . '/uploads';
        $count = 1; // variable to set replace count

        $video = Video::findOrFail($video_id);

        $video->delete();
        $video_dir = $base_dir . '/videos/';
        $file_info = pathinfo($video->file_path);
        $files_dir = str_replace('source-', '', $file_info['filename'], $count);

        Storage::disk('s3')->deleteDirectory($video_dir . $files_dir);

        return $video;
    }
}
