<?php

namespace App\GraphQL\Mutation\Slide;

use App\Models\Slide;
use App\Models\Text;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class UpdateSlideMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateSlide',
    ];

    public function type()
    {
        return GraphQL::type('Slide');
    }

    public function args()
    {
        return [
            'slide'     => ['name' => 'slide', 'type' => GraphQL::type('SlideInput')],
        ];
    }

    public function resolve($root, $args)
    {
        $texts = [];

        $slide = Slide::findOrFail($args['slide']['id']);

        $slide_data = [
            'project_id' => $args['slide']['projectId'],
            'name' => $args['slide']['name'],
            'effects' => $args['slide']['effects'],
        ];

        $slide->update($slide_data);

        $text_ids = $slide->texts->pluck('id')->toArray();
        foreach ($args['slide']['texts'] as $text) {
            $texts[] = [
                'id'         => (in_array($text['id'] ?? 0, $text_ids)) ? $text['id'] : 0,
                'project_id' => $args['slide']['projectId'],
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

        foreach ($slide->texts as $text) {
            if (!in_array($text->id, collect($texts)->pluck('id')->toArray())) {
                $text->delete();
            }
        }

        foreach ($texts as $text_data) {
            $existed_text = Text::find($text_data['id']);
            if ($existed_text) {
                $existed_text->update($text_data);
            } else {
                Text::create($text_data);
            }
        }

        return $slide->fresh();
    }
}
