<?php

namespace App\GraphQL\Query\Post;

use App\Helpers\AuthHelper;
use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;
use App\Models\Post;

class PostsQuery extends Query
{
    const PAGINATION_COUNT = 10;

    protected $attributes = [
        'name' => 'posts',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Post'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'slug' => ['name' => 'description', 'type' => Type::string()],
            'content' => ['name' => 'title', 'type' => Type::string()],
            'author_id' => ['name' => 'author_id', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        $user = AuthHelper::me();
        $query = Post::query();

        $query->performancePosts()
            ->skipFlagged($user)
            ->with('videos', 'images', 'author', 'stars')
            ->latest()
            ->paginate(self::PAGINATION_COUNT);

        return $query->get();
    }
}
