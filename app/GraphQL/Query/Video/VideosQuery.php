<?php

namespace App\GraphQL\Query\Video;

use Folklore\GraphQL\Support\Query;
use GraphQL;
use GraphQL\Type\Definition\Type;
use App\Models\Video;

class VideosQuery extends Query
{

    protected $attributes = [
        'name' => 'videos',
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Video'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'authorId' => ['name' => 'authorId', 'type' => Type::int()],
            'parentId' => ['name' => 'parentId', 'type' => Type::int()],
            'isUploaded' => ['name' => 'isUploaded', 'type' => Type::boolean()],
            'isSaved' => ['name' => 'isSaved', 'type' => Type::boolean()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Video::query();

        if (!isset($args['isUploaded'])) {
            $args['isUploaded'] = false;
        }

        if (!isset($args['isSaved'])) {
            $args['isSaved'] = false;
        }

        if (isset($args['authorId'])) {
            $query->where('author_id', $args['authorId']);
        }

        if ($args['isUploaded'] && $args['isSaved']) {
            return $query->get();
        }

        if ($args['isUploaded']) {
            $query->whereNull('parent_id');
        } elseif ($args['isSaved']) {
            $query->whereNotNull('parent_id');
        }

        return $query->get();
    }
}
