<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class SlideEffectsType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'SlideEffects',
        'description' => 'A slide',
    ];

    public function fields()
    {

        return [
            'fadeIn' => [
                'type'        => GraphQL::type('EffectOptions'),
                'description' => 'Slide effect',
            ],
            'fadeOut' => [
                'type'        => GraphQL::type('EffectOptions'),
                'description' => 'Slide effect',
            ],
        ];
    }
}
