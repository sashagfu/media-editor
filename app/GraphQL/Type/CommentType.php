<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use App\Models\Post;
use GraphQL;
use GraphQL\Type\Definition\Type;

class CommentType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Comment',
        'description' => 'A comment',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
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
            'author' => [
                'type' => GraphQL::type('User'),
                'description' => 'The author of comment',
            ],
            'authorId' => [
                'type'        => Type::int(),
                'description' => 'Id of the author',
                'resolve'     => function ($post) {
                    return $post->author_id;
                },
            ],
            'projectId' => [
                'type'        => Type::int(),
                'description' => 'Id of the commented post',
                'resolve'     => function ($comment) {
                    return $comment->project_id;
                }
            ],
            'parentId' => [
                'type'        => Type::int(),
                'description' => 'Id of the parent comment (if this is the reply)',
                'resolve'     => function ($comment) {
                    return $comment->parent_id;
                }
            ],
            'likes' => [
                'type'        => Type::listOf(GraphQL::type('User')),
                'description' => 'List of users which liked comment',
            ],
            'replies' => [
                'type'        => Type::listOf(GraphQL::type('Comment')),
                'description' => 'List of replies',
            ]
        ];
    }
}
