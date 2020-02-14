<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\Slide;
use App\Models\Text;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SlideMutator
{
    public function create($root, $args)
    {
        $slide = new Slide();
        $slide->project_id = $args['projectId'];
        $slide->name = $args['slide']['name'];
        $slide->effects = $args['slide']['effects'] ?: Slide::DEFAULT_EFFECTS;
        $slide->save();

        foreach ($args['slide']['texts'] as $data) {
            $text = new Text();
            $text->project_id = $args['projectId'];
            $text->slide_id = $slide->id;
            $text->content = $data['text'];
            $text->font = $data['fontFamily'];
            $text->font_type = ($data['fontStyle'] == 'normal') ? 'Regular' : $data['fontStyle'];
            $text->position = $data['position'];
            $text->size = $data['fontSize'];
            $text->align = $data['textAlign'];
            $text->color = $data['color'] ?: Text::DEFAULT_COLOR;
            $text->box_size = $data['size'];
            $text->save();
        }

        return $slide;
    }

    public function update($root, $args)
    {
        $texts = [];

        $slide = Slide::findOrFail($args['slide']['id']);

        $slide->project_id = $args['slide']['projectId'];
        $slide->name = $args['slide']['name'];
        $slide->effects = $args['slide']['effects'] ?: Slide::DEFAULT_EFFECTS;
        $slide->save();

        $text_ids = $slide->texts->pluck('id')->toArray();
        foreach ($args['slide']['texts'] as $text) {
            $texts[] = [
                'id'         => (in_array($text['id'] ?? 0, $text_ids)) ? $text['id'] : 0,
                'project_id' => $args['slide']['projectId'],
                'slide_id'   => $slide->id,
                'content'    => $text['text'],
                'font'       => $text['fontFamily'],
                'font_type'  => ($text['fontStyle'] == 'normal') ? 'Regular' : $text['fontStyle'],
                'position'   => $text['position'],
                'size'       => $text['fontSize'],
                'align'      => $text['textAlign'],
                'color'      => $text['color'] ?: Text::DEFAULT_COLOR,
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
