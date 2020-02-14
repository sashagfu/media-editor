<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Star;
use App\Models\Post;
use App\Helpers\DBHelper;
use App\Helpers\AuthHelper;
use App\Notifications\StarNotification;
use Carbon\Carbon;

class StarController extends Controller
{
    public function handleStar(Request $request)
    {
        $user_id = AuthHelper::myId();
        $post_id = $request->post_id;
        $type = DBHelper::getMapByModel(Post::class);
        $post_author = Post::find($post_id)->author;

        $existing_star = Star::whereStarableType($type)
            ->whereStarableId($post_id)
            ->whereUserId($user_id)
            ->first();

        if (!$existing_star) {
            $star = new Star();
            $star->user_id = $user_id;
            $star->starable_id = $post_id;
            $star->starable_type = $type;
            $star->save();

            $post_author->total_stars++;
            $post_author->save();

            if ($post_author->id != $user_id) {
                $post_author->notify(new StarNotification($star));
            }

            return [
                'deleted' => false,
                'star'    => $star->user,
                'star_id' => $star->id
            ];
        } else {
            $existing_star->delete();
            return [
                'deleted' => true,
                'star'    => $existing_star->user,
            ];
        }
    }

    public function showPostStars(Request $request)
    {
        $post_id = $request->post_id;
        $stars = Post::find($post_id)->stars;

        if (!$stars->isEmpty()) {
            foreach ($stars as $user) {
                $users[] = $user->setVisible(['id', 'username', 'display_name', 'avatar']);
            }

            return ['users' => $users];
        } else {
            return ['error' => trans('stars.empty')];
        }
    }
}
