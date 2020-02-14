<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class EffectOptionsType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'EffectOptions',
        'description' => 'A slide',
    ];

    public function fields()
    {

        return [
            'duration' => [
                'type'        => Type::float(),
                'description' => 'Duration of the effect',
            ],
        ];
    }
}
