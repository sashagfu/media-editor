<?php

namespace App\GraphQL\InputTypes;

use GraphQL;
use Folklore\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;

class ProjectInputType extends InputType
{
    protected $attributes = [
        'name'        => 'ProjectInput',
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
            'title' => [
                'type'        => Type::string(),
                'description' => 'The title of the project',
            ],
            'description' => [
                'type'        => Type::string(),
                'description' => 'The description of the project',
            ],
            'isDraft' => [
                'type' => Type::boolean(),
                'description' => 'If project is draft',
            ],
            'tags' => [
                'type'        => Type::listOf(GraphQL::type('TagInput')),
                'description' => 'List of tags',
            ],
            'isProcessing' => [
                'type'        => Type::boolean(),
                'description' => 'If project is in processing',
            ],
            'isPublished' => [
                'type'        => Type::boolean(),
                'description' => 'If status is published',
            ],
            'isFailed' => [
                'type'        => Type::int(),
                'description' => 'If status is failed',
            ],
            'progress' => [
                'type'        => Type::int(),
                'description' => 'The publish progress',
            ],
        ];
    }
}
