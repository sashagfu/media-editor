<?php

namespace App\GraphQL\InputTypes;

use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;

class TagInputType extends InputType
{
    protected $attributes = [
        'name'        => 'TagInput',
        'description' => 'A project',
    ];

    /*
    * Uncomment following line to make the type input object.
    * http://graphql.org/learn/schema/#input-types
    */
    protected $inputObject = true;

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::int(),
                'description' => 'The id of the project',
            ],
            'name' => [
                'type'        => Type::string(),
                'description' => 'The title of the project',
            ]
        ];
    }
}
