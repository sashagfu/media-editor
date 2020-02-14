<?php

namespace App\Http\Controllers\Api;

use User;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Notifications\UserFollowingNotification;

class UserFollowingsController extends Controller
{
    public function handleUserFollowing(Request $request)
    {
        $user = AuthHelper::me();
        $follower_id = $request->follower_id;
        $follower = User::find($follower_id);
        $existing_follower = $user->following->contains($follower_id);

        if ($existing_follower) {
            $user->following()->detach($follower_id);
            return trans('profiles.follow');
        } else {
            $user->following()->attach($follower_id);
            $follower->notify(new UserFollowingNotification($user));
            return trans('profiles.unfollow');
        }
    }
}
