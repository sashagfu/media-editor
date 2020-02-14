<?php

namespace App\GraphQL\Type\Text;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class TextPositionType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'TextPosition',
        'description' => 'A text position',
    ];

    public function fields()
    {

        return [
            'x' => [
                'type' => Type::int(),
                'description' => 'The x coordinate',
            ],
            'y' => [
                'type'        => Type::int(),
                'description' => 'The y coordinate',
            ],
        ];
    }
}
