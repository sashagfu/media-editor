<?php

namespace App\GraphQL\Type;

use App\Models\Slide;
use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class SlideType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Slide',
        'description' => 'A slide',
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
                'description' => 'The id of project',
                'resolve' => function ($slide) {
                    return $slide->project_id;
                }
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of the slide',
            ],
            'fileType' => [
                'type' => Type::string(),
                'description' => 'Morph type of the slide',
                'resolve' => function () {
                    return Slide::MORPH_TYPE;
                }
            ],
            'effects' => [
                'type' => GraphQL::type('SlideEffects'),
                'description' => 'Slide effects',
            ],
            'texts' => [
                'type' => Type::listOf(GraphQL::type('Text')),
            ],
        ];
    }
}
