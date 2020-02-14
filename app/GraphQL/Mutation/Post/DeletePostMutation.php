<?php
/**
 * Created by PhpStorm.
 * User: vlas
 * Date: 26.06.18
 * Time: 10:47
 */

namespace App\GraphQL\Mutation\Post;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class DeletePostMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deletePost',
    ];

    public function type()
    {
        return GraphQL::type('Post');
    }

    public function args()
    {
        return [
            'postId' => ['name' => 'postId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'postId' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = AuthHelper::me();

        $post_id = $args['postId'];

        $post = Post::findOrFail($post_id);

        if ($post->author->id === $user->id) {
            $post->delete();

            return null;
        }
        return \Response::json(trans('common.error_reload'), 422);
    }
}
