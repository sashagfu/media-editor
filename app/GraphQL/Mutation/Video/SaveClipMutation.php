<?php

namespace App\GraphQL\Mutation\Video;

use App\Helpers\AuthHelper;
use App\Models\Video;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class SaveClipMutation extends Mutation
{
    protected $attributes = [
        'name' => 'saveClip',
    ];

    public function type()
    {
        return GraphQL::type('Video');
    }

    public function args()
    {
        return [
            'videoId'     => ['name' => 'videoId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'videoId'      => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = AuthHelper::me();

        $parent_video = Video::findOrfail($args['videoId']);
        if ($user->hasSavedVideo($parent_video->id)) {
            return Video::where('author_id', $user->id)
                ->where('parent_id', $parent_video->id)
                ->first();
        }
        $video_data = array_merge(
            $parent_video->toArray(),
            [
                'parent_id' => $parent_video->id,
                'author_id' => $user->id,
            ]
        );

        $video = Video::create($video_data);

        return $video;
    }
}
