<?php

namespace App\GraphQL\InputTypes;

use Folklore\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CommentInputType extends InputType
{
    protected $attributes = [
        'name'        => 'CommentInput',
        'description' => 'A comment',
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
                'description' => 'The if of the comment',
            ],
            'text'      => [
                'type'        => Type::string(),
                'description' => 'Text of the post',
            ],
            'isLiked'      => [
                'type'        => Type::boolean(),
                'description' => 'If comment is liked',
            ],
            'createdAtDiff' => [
                'type'        => Type::string(),
                'description' => 'Created at date',
            ],
            'repliesLength' => [
                'type'        => Type::int(),
                'description' => 'Count of the replies',
            ],
            'authorId' => [
                'type'        => Type::int(),
                'description' => 'Id of the author',
            ],
            'projectId' => [
                'type'        => Type::int(),
                'description' => 'Id of the commented post',
            ],
            'parentId' => [
                'type'        => Type::int(),
                'description' => 'Id of the parent comment (if this is the reply)',
            ],
        ];
    }
}
