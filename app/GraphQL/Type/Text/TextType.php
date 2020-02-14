<?php

namespace App\GraphQL\Type\Text;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class TextType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Text',
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
                'description' => 'The id of project',
                'resolve' => function ($text) {
                    return $text->project_id;
                }
            ],
            'slideId' => [
                'type' => Type::int(),
                'description' => 'The id of the slide',
                'resolve' => function ($text) {
                    return $text->slide_id;
                }
            ],
            'text'      => [
                'type'        => Type::string(),
                'description' => 'Type content of the text',
                'resolve' => function ($text) {
                    return $text->content;
                }
            ],
            'fontFamily' => [
                'type'        => Type::string(),
                'description' => 'Font of the text',
                'resolve' => function ($text) {
                    return $text->font;
                }
            ],
            'fontStyle'   => [
                'type'        => Type::string(),
                'description' => 'Type of the font',
                'resolve' => function ($text) {
                    return $text->font_type;
                }
            ],
            'position' => [
                'type' => GraphQL::type('TextPosition'),
                'description' => 'The position of the text in slide',
            ],
            'fontSize'   => [
                'type'        => Type::float(),
                'description' => 'Size of the font',
                'resolve' => function ($text) {
                    return $text->size;
                }
            ],
            'fontWeight' => [
                'type'  => Type::string(),
                'description' => 'Weight of the font',
            ],
            'textAlign' => [
                'type' => Type::string(),
                'description' => 'Alignment of the text',
                'resolve' => function ($text) {
                    return $text->align;
                }
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'Color of the text',
                'resolve' => function ($text) {
                    return $text->getOriginal('color');
                }
            ],
            'background' => [
                'type' => Type::string(),
                'description' => 'Background of the text',
            ],
            'size' => [
                'type' => GraphQL::type('TextBoxSize'),
                'description' => 'Size of the text box',
                'resolve' => function ($text) {
                    return $text->box_size;
                }
            ]
        ];
    }
}
