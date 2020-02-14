<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use App\Models\Post;
use GraphQL;
use GraphQL\Type\Definition\Type;

class TagType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Tag',
        'description' => 'A tag',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The if of the tag',
            ],
            'name'      => [
                'type'        => Type::string(),
                'description' => 'Name of the tag',
            ],
        ];
    }
}
