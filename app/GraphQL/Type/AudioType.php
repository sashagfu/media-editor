<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class AudioType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Audio',
        'description' => 'An audio',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The if of the audio',
            ],
            'name'      => [
                'type'        => Type::string(),
                'description' => 'Name of the audio',
            ],
            'sprite' => [
                'type'        => Type::string(),
                'description' => 'Sprite of the audio',
            ],
            'filePath' => [
                'type'        => Type::string(),
                'description' => 'Video path',
                'resolve'     => function ($video) {
                    return $video->file_path;
                },
            ],
            'time'      => [
                'type'        => Type::string(),
                'description' => 'Time of the audio',
            ]
        ];
    }
}
