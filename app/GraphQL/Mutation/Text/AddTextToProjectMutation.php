<?php

namespace App\GraphQL\Mutation\Text;

use App\Models\Text;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AddTextToProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addTextToProject',
    ];

    public function type()
    {
        return GraphQL::type('Text');
    }

    public function args()
    {
        return [
            'text' => ['name' => 'text', 'type' => GraphQL::type('TextInput')],
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'projectId' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $text = $args['text'];

        $text_data = [
            'project_id' => $args['projectId'],
            'content' => $text['content'],
            'font' => $text['font'],
            'font_type' => $text['fontStyle'],
            'size' => $text['fontSize'],
            'align' => $text['textAlign'],
            'color' => $text['color'],
//            'background' => $text['background'],
        ];

        $text = Text::create($text_data);

        return $text;
    }
}
