<?php

namespace App\GraphQL\Mutation\Video;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Audio;
use App\Models\Image;
use App\Models\Video;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Support\Facades\Storage;

class DeleteVideoFromProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteVideoFromProject',
    ];

    public function type()
    {
        return GraphQL::type('Video');
    }

    public function args()
    {
        return [
            'videoId' => ['name' => 'videoId', 'type' => Type::int()],
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'videoId' => [
                'required',
            ],
            'projectId' => [
                'required',
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $video_id = $args['videoId'];
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
