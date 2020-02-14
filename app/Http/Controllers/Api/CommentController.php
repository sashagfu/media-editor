<?php

namespace App\Http\Controllers\Api;

use App\Notifications\CommentNotification;
use App\Notifications\CommentReplyNotification;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Response;
use Auth;
use App\Helpers\DBHelper;
use App\Helpers\AuthHelper;

class CommentController extends Controller
{
    const COMMENT_PAGINATION = 5;

    public function addComment(Request $request)
    {
        $this->validate(
            $request,
            [
            'comment_text' => 'required',
            ]
        );

        $user = AuthHelper::me();
        $post_id = $request->post_id;
        $post = Post::find($post_id);
        $post_author = $post->author;
        $comment_text = $request->comment_text;

        $comment = new Comment();
        $comment->author_id = $user->id;
        $comment->post_id = $post_id;
        $comment->text = $comment_text;
        $comment->parent_id = null;
        $comment->save();

        if ($user->id != $post_author->id) {
            $post_author->notify(new CommentNotification($post, $user));
        }

        return [
            'comment'       => $comment,
            'commentsCount' => $post->commentsCount,
        ];
    }

    public function notifyComment(Request $request)
    {
        $user = AuthHelper::me();
        $user_id = AuthHelper::myId();
        $post_id = $request->post_id;
        $post = Post::find($post_id);
        $post_author = $post->author;

        if ($user_id != $post_author->id) {
            $post_author->notify(new CommentNotification($post, $user));
        }
    }

    public function addReply(Request $request)
    {
        $this->validate(
            $request,
            [
            'comment_text' => 'required',
            'parent_id'    => 'required',
            ]
        );

        $user = AuthHelper::me();
        $post_id = $request->post_id;
        $parent_id = $request->parent_id;
        $post = Post::find($post_id);
        $parent_comment = Comment::find($parent_id);
        $parent_comment_author = $parent_comment->author;
        $comment_text = $request->comment_text;
        $parent_id = $request->parent_id;

        $comment = new Comment();
        $comment->author_id = $user->id;
        $comment->post_id = $post_id;
        $comment->text = $comment_text;

        $comment->parent_id = $parent_id;
        $comment->save();

        if ($user->id != $parent_comment_author->id) {
            $parent_comment_author->notify(new CommentReplyNotification($post, $user));
        }
        return $comment;
    }

    public function getCommentsForPost(Request $request)
    {
        $this->validate(
            $request,
            [
            'post_id' => 'required',
            ]
        );

        $post_id = $request->post_id;

        return Post::find($post_id)->comments()->latest()->paginate(self::COMMENT_PAGINATION)->toJson();
    }

    public function getCommentReplies(Request $request)
    {
        $this->validate(
            $request,
            [
            'comment_id' => 'required',
            ]
        );

        $comment_id = $request->comment_id;

        return Comment::find($comment_id)->replies()->latest()->paginate(self::COMMENT_PAGINATION)->toJson();
    }

    public function deleteComment(Request $request)
    {
        $this->validate(
            $request,
            [
            'comment_id' => 'required',
            ]
        );

        $user = AuthHelper::me();

        $comment_id = $request->comment_id;

        $comemnt = Comment::findOrFail($comment_id);

        if (!$comemnt->parent_id) {
            $comemnt->replies()->delete();
        }

        if ($comemnt->author->id === $user->id) {
            $comemnt->delete();

            return [
                'is_success' => true
            ];
        }

        return \Response::json(trans('common.error_reload'), 422);
    }
}
