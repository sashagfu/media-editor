<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use App\Models\Post;
use GraphQL;
use GraphQL\Type\Definition\Type;

class ImageType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Image',
        'description' => 'A image',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The id of the image',
            ],
            'fileName' => [
                'type'        => Type::string(),
                'description' => 'The slug of the post',
                'resolve'     => function ($image) {
                    return $image->file_name;
                },
            ],
            'mediaType' => [
                'type'        => Type::string(),
                'description' => 'Polymorphic type of the image',
            ],
            'filePath'      => [
                'type'        => Type::string(),
                'description' => 'Path of the post',
                'resolve'     => function ($image) {
                    return $image->file_path;
                },
            ],
            'width' => [
                'type'        => Type::int(),
                'description' => 'Width of the image',
            ],
            'height' => [
                'type'        => Type::int(),
                'description' => 'Height of the image',
            ],
            'fileSize' => [
                'type'        => Type::int(),
                'description' => 'Size of the image',
                'resolve' => function ($image) {
                    return $image->file_size;
                }
            ]
        ];
    }
}
