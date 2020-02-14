<?php
/**
 * Created by PhpStorm.
 * User: vlas
 * Date: 26.06.18
 * Time: 10:47
 */

namespace App\GraphQL\Mutation\Like;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\LikeNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class HandlePostLikeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'handlePostLike',
    ];

    public function type()
    {
        return GraphQL::type('User');
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
            'postId'      => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user_id = AuthHelper::myId();
        $post_id = $args['postId'];
        $type = DBHelper::getMapByModel(Post::class);
        $post_author = Post::find($post_id)->author;

        $existing_like = Like::whereLikeableType($type)
            ->whereLikeableId($post_id)
            ->whereUserId($user_id)
            ->first();

        if (!$existing_like) {
            $like = new Like();
            $like->user_id = $user_id;
            $like->likeable_id = $post_id;
            $like->likeable_type = $type;
            $like->save();

            $post_author->total_likes++;
            $post_author->save();

            if ($post_author->id != $user_id) {
                $post_author->notify(new LikeNotification($like));
            }

            return $like->user;
        } else {
            $existing_like->delete();
            return null;
        }
    }
}
