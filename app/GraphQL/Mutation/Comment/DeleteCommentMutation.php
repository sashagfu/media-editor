<?php
/**
 * Created by PhpStorm.
 * User: vlas
 * Date: 26.06.18
 * Time: 10:47
 */

namespace App\GraphQL\Mutation\Comment;

use App\Helpers\AuthHelper;
use App\Models\Comment;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class DeleteCommentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteComment',
    ];

    public function type()
    {
        return GraphQL::type('Comment');
    }

    public function args()
    {
        return [
            'commentId' => ['name' => 'commentId', 'type' => Type::nonNull(Type::int())],
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
        $user = AuthHelper::me();

        $comment_id = $args['commentId'];

        $comment = Comment::findOrFail($comment_id);

        if (!$comment->parent_id) {
            $comment->replies()->delete();
        }

        if ($comment->author->id === $user->id) {
            $comment->delete();

            return null;
        }

        return \Response::json(trans('common.error_reload'), 422);
    }
}
