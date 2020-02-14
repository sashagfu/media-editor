<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class VideoType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Video',
        'description' => 'A video',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The if of the video',
            ],
            'isPerformance' => [
                'type'        => Type::boolean(),
                'description' => 'Is video is performance',
                'resolve'     => function ($video) {
                    return $video->is_performance;
                },
            ],
            'authorId' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The id of the author',
                'resolve'     => function ($video) {
                    return $video->author_id;
                },
            ],
            'author' => [
                'type'        => GraphQL::type('User'),
                'description' => 'The author of the video',
            ],
            'parentId' => [
                'type'        => Type::int(),
                'description' => 'The id of the video which user saved',
                'resolve'     => function ($video) {
                    return $video->parent_id;
                }
            ],
            'name'      => [
                'type'        => Type::string(),
                'description' => 'Name of the video',
            ],
            'spritePath' => [
                'type'        => Type::string(),
                'description' => 'Sprite path of the video',
                'resolve'     => function ($video) {
                    return $video->sprite_path;
                },
            ],
            'filePath' => [
                'type'        => Type::string(),
                'description' => 'Video path',
                'resolve'     => function ($video) {
                    return $video->file_path;
                },
            ],
            'thumbnailPath'  => [
                'type'        => Type::string(),
                'description' => 'Thumbnail path of video',
                'resolve'     => function ($video) {
                    return $video->thumbnail_path;
                },
            ],
            'time'      => [
                'type'        => Type::string(),
                'description' => 'Time of video',
            ]
        ];
    }
}
