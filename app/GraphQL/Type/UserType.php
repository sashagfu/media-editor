<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use App\Models\Post;
use GraphQL;
use GraphQL\Type\Definition\Type;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'User',
        'description' => 'A user',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'The id of the user',
            ],
            'username' => [
                'type'        => Type::string(),
                'description' => 'The username of the user',
            ],
            'email'      => [
                'type'        => Type::string(),
                'description' => 'Email of the user',
            ],
            'displayName'      => [
                'type'        => Type::string(),
                'description' => 'Display name of the user',
                'resolve'     => function ($user) {
                    return $user->display_name;
                },
            ],
            'talent'      => [
                'type'        => Type::string(),
                'description' => 'Talent of the user',
            ],
            'avatar'      => [
                'type'        => Type::string(),
                'description' => 'Url to the user avatar',
            ],
            'isFollowing' => [
                'type'        => Type::boolean(),
                'description' => 'If the user is following',
            ],
            'canBeFollowed' => [
                'type'        => Type::boolean(),
                'description' => 'If the user can be followed',
            ],
            'createdAt'      => [
                'type'        => Type::string(),
                'description' => 'Date users create',
                'resolve'     => function ($user) {
                    return $user->created_at->toDateTimeString();
                },
            ],
        ];
    }
}
