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
use App\Models\Comment;
use App\Models\Like;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class HandleCommentLikeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'handleCommentLike',
    ];

    public function type()
    {
        return GraphQL::type('User');
    }

    public function args()
    {
        return [
            'commentId' => ['name' => 'commentId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'commentId'      => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user_id = AuthHelper::myId();
        $comment_id = $args['commentId'];
        $type = DBHelper::getMapByModel(Comment::class);

        $existing_like = Like::whereLikeableType($type)
            ->whereLikeableId($comment_id)
            ->whereUserId($user_id)
            ->first();

        if (!$existing_like) {
            $like = new Like();
            $like->user_id = $user_id;
            $like->likeable_id = $comment_id;
            $like->likeable_type = $type;
            $like->save();
            return $like->user;
        } else {
            $existing_like->delete();

            return null;
        }
    }
}
