<?php

namespace App\GraphQL\Type\Text;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class TextBoxSizeType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'TextBoxSize',
        'description' => 'Size of the text box',
    ];

    public function fields()
    {

        return [
            'h' => [
                'type' => Type::float(),
                'description' => 'The height',
                'resolve' => function () {
                    return 46;
                }
            ],
            'w' => [
                'type'        => Type::float(),
                'description' => 'The width',
                'resolve' => function () {
                    return 133.890625;
                }
            ],
        ];
    }
}
