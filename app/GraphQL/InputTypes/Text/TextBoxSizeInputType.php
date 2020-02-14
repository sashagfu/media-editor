<?php

namespace App\GraphQL\InputTypes\Text;

use Folklore\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;

class TextBoxSizeInputType extends InputType
{
    protected $attributes = [
        'name'        => 'TextBoxSizeInput',
        'description' => 'Size of the text box',
    ];

    public function fields()
    {

        return [
            'h' => [
                'type' => Type::float(),
                'description' => 'The height',
            ],
            'w' => [
                'type'        => Type::float(),
                'description' => 'The width',
            ],
        ];
    }
}
