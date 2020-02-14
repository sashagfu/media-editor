<?php

namespace App\GraphQL\InputTypes\Text;

use Folklore\GraphQL\Support\InputType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class TextInputType extends InputType
{
    protected $attributes = [
        'name'        => 'TextInput',
        'description' => 'A text',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::int(),
                'description' => 'The id of the text',
            ],
            'projectId' => [
                'type'        => Type::int(),
                'description' => 'The id of text',
            ],
            'slideId' => [
                'type'        => Type::int(),
                'description' => 'The id of the slide',
            ],
            'text'      => [
                'type'        => Type::string(),
                'description' => 'Type content of the text',
            ],
            'fontFamily' => [
                'type'        => Type::string(),
                'description' => 'Font of the text',
            ],
            'fontStyle'   => [
                'type'        => Type::string(),
                'description' => 'Type of the font',
            ],
            'position' => [
                'type' => GraphQL::type('TextPositionInput'),
                'description' => 'The position of the text in slide',
            ],
            'fontSize'   => [
                'type'        => Type::float(),
                'description' => 'Size of the font',
            ],
            'fontWeight' => [
                'type'        => Type::string(),
                'description' => 'Weight of the font',
            ],
            'textAlign' => [
                'type' => Type::string(),
                'description' => 'Alignment of the text',
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'Color of the text',
            ],
            'background' => [
                'type' => Type::string(),
                'description' => 'Background of the text',
            ],
            'size' => [
                'type' => GraphQL::type('TextBoxSizeInput'),
                'description' => 'Size of the text box'
            ]
        ];
    }
}
