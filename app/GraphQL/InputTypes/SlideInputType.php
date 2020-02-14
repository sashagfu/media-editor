<?php

namespace App\GraphQL\InputTypes;

use Folklore\GraphQL\Support\InputType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class SlideInputType extends InputType
{
    protected $attributes = [
        'name'        => 'SlideInput',
        'description' => 'A slide',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::int(),
                'description' => 'The id of the slide',
            ],
            'projectId' => [
                'type'        => Type::int(),
                'description' => 'The id of project',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of the slide',
            ],
            'fileType' => [
                'type' => Type::string(),
                'description' => 'Morph type of the slide',
            ],
            'effects' => [
                'type' => GraphQL::type('SlideEffectsInput'),
                'description' => 'Slide effects',
            ],
            'texts' => [
                'type' => Type::listOf(GraphQL::type('TextInput')),
                'description' => 'The list of the texts related to slide',
            ],
        ];
    }
}
