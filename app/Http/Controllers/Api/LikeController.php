<?php

namespace App\Http\Controllers\Api;

use App\Notifications\LikeNotification;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use App\Helpers\DBHelper;
use App\Helpers\AuthHelper;

class LikeController extends Controller
{
    public function handlePostLike(Request $request)
    {
        $user_id = AuthHelper::myId();
        $post_id = $request->post_id;
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

            return [
                'deleted' => false,
                'like'    => $like->user,
                'like_id' => $like->id,
            ];
        } else {
            $existing_like->delete();

            return [
                'deleted' => true,
                'like'    => $existing_like->user,
            ];
        }
    }

    public function handleCommentLike(Request $request)
    {
        $user_id = AuthHelper::myId();
        $comment_id = $request->comment_id;
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
            return [
                'deleted' => false,
                'like'    => $like->user,
            ];
        } else {
            $existing_like->delete();

            return [
                'deleted' => true,
                'like'    => $existing_like->user,
            ];
        }
    }

    public function showPostLikes(Request $request)
    {
        $post_id = $request->post_id;
        $likes = Post::find($post_id)->likes;

        if (!$likes->isEmpty()) {
            foreach ($likes as $user) {
                $users[] = $user->setVisible(['id', 'username', 'display_name', 'avatar']);
            }
            return['users' => $users];
        } else {
            return ['error' => trans('likes.empty')];
        }
    }

    public function showCommentLikes(Request $request)
    {
        $comment_id = $request->comment_id;
        $likes = Comment::find($comment_id)->likes;

        if (!$likes->isEmpty()) {
            foreach ($likes as $user) {
                $users[] = $user->setVisible(['id', 'username', 'display_name']);
            }
            return['users' => $users];
        } else {
            return ['error' => trans('likes.empty')];
        }
    }
}
