<?php

namespace App\GraphQL\Mutation\Slide;

use App\Models\Slide;
use App\Models\Text;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AddSlideToProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addSlideToProject',
    ];

    public function type()
    {
        return GraphQL::type('Slide');
    }

    public function args()
    {
        return [
            'slide'     => ['name' => 'slide', 'type' => GraphQL::type('SlideInput')],
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
        $texts = [];

        $slide_data = [
            'project_id' => $args['projectId'],
            'name' => $args['slide']['name'],
            'effects' => $args['slide']['effects'],
        ];

        $slide = Slide::create($slide_data);

        foreach ($args['slide']['texts'] as $text) {
            $texts[] = [
                'project_id' => $args['projectId'],
                'slide_id'   => $slide->id,
                'content'    => $text['text'],
                'font'       => $text['fontFamily'],
                'font_type'  => $text['fontStyle'],
                'position'   => $text['position'],
                'size'       => $text['fontSize'],
                'align'      => $text['textAlign'],
                'color'      => $text['color'],
                'box_size'   => $text['size'],
                //            'background' => $text['background'],
            ];
        }

        foreach ($texts as $text_data) {
            Text::create($text_data);
        }

        return $slide;
    }
}
