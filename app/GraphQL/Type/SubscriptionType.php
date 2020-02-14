<?php

namespace App\GraphQL\Type;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;

class SubscriptionType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Subscription',
        'description' => 'An subscription',
    ];

    public function fields()
    {

        return [
            'projectPublished' => [
                'type'        => GraphQL::type('Project'),
                'description' => 'Project generated notification',
            ],
        ];
    }
}
