<?php

namespace App\GraphQL\InputTypes;

use Folklore\GraphQL\Support\InputType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class SlideEffectsInputType extends InputType
{
    protected $attributes = [
        'name'        => 'SlideEffectsInput',
        'description' => 'A slide',
    ];

    public function fields()
    {

        return [
            'fadeIn' => [
                'type'        => GraphQL::type('EffectOptionsInput'),
                'description' => 'Slide effect',
            ],
            'fadeOut' => [
                'type'        => GraphQL::type('EffectOptionsInput'),
                'description' => 'Slide effect',
            ],
        ];
    }
}
