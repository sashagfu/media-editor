<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use App\Models\Post;
use GraphQL;
use GraphQL\Type\Definition\Type;

class PostType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Post',
        'description' => 'A post',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The if of the post',
            ],
            'slug' => [
                'type'        => Type::string(),
                'description' => 'The slug of the post',
            ],
            'content'      => [
                'type'        => Type::string(),
                'description' => 'Title of the post',
            ],
            'isPerformance'      => [
                'type'        => Type::boolean(),
                'description' => 'Is performance',
            ],
            'author' => [
                'type' => GraphQL::type('User'),
                'description' => 'The author of post',
            ],
            'authorId' => [
                'type'        => Type::int(),
                'description' => 'Id of the author',
                'resolve'     => function ($post) {
                    return $post->author_id;
                },
            ],
            'commentVisibility' => [
                'type'        => Type::boolean(),
                'description' => 'If comments a visible',
            ],
            'commentsCount' => [
                'type'        => Type::int(),
                'description' => 'Comments count',
            ],
            'parsedContent' => [
                'type'        => Type::string(),
                'description' => 'Parsed content',
            ],
            'createdAtDiff' => [
                'type'        => Type::string(),
                'description' => 'Created at date',
            ],
            'userReaction'  => [
                'type'        => Type::boolean(),
                'description' => 'User reaction',
            ],
            'shareable' => [
                'type'        => Type::boolean(),
                'description' => 'If post is shareable',
            ],
            'videos' => [
                'type'        => Type::listOf(GraphQL::type('Video')),
                'description' => 'Videos related to post',
            ],
            'images' => [
                'type'        => Type::listOf(GraphQL::type('Image')),
                'description' => 'Images related to post'
            ],
            'stars' => [
                'type'        => Type::listOf(GraphQL::type('User')),
                'description' => 'List of users stared the post',
            ],
            'likes' => [
                'type'        => Type::listOf(GraphQL::type('User')),
                'description' => 'List of users which liked post',
            ],
            'comments' => [
                'type'        => Type::listOf(GraphQL::type('Comment')),
                'description' => 'List of comments',
            ],
        ];
    }
}
