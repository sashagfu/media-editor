<?php

namespace App\GraphQL\Mutation\Slide;

use App\Models\Slide;
use App\Models\Text;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class DeleteSlideMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteSlide',
    ];

    public function type()
    {
        return GraphQL::type('Slide');
    }

    public function args()
    {
        return [
            'slideId' => ['name' => 'slideId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'slideId' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $slide = Slide::findOrFail($args['slideId']);

        $slide->delete();

        return $slide;
    }
}
