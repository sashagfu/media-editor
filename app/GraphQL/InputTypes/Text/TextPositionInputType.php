<?php

namespace App\GraphQL\InputTypes\Text;

use Folklore\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;

class TextPositionInputType extends InputType
{
    protected $attributes = [
        'name'        => 'TextPositionInput',
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
