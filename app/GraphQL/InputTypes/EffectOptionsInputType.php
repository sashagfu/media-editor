<?php

namespace App\GraphQL\InputTypes;

use Folklore\GraphQL\Support\InputType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class EffectOptionsInputType extends InputType
{
    protected $attributes = [
        'name'        => 'EffectOptionsInput',
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
