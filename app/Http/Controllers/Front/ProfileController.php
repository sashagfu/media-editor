<?php

namespace App\Http\Controllers\Front;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\User;
use App\Models\FlagReason;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\Javascript;

class ProfileController extends Controller
{
    const PAGINATION_COUNT = 10;

    public function mine()
    {
        $user = AuthHelper::me();
        $socials = $user->getSetting('socials');
        $flag_reasons = FlagReason::enabled()->get();
        $feed_items = $user->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest('pivot_updated_at')
            ->paginate(self::PAGINATION_COUNT)
            ->toJson();

        return view('front.my-profile')->with(
            [
            'me'            => $user,
            'videos'        => $user->videos->sortByDesc('created_at')->take(3),
            'last_avatar'   => $user->avatars->last(),
            'feed_items'    => $feed_items,
            'user_location' => $user->getSetting('location'),
            'socials'       => collect($socials)->toJson(),
            'flag_reasons'  => $flag_reasons,
            ]
        );
    }

    public function anotherProfile($user_name)
    {
        $user = User::whereUsername($user_name)->with('following')->firstOrFail();
        $location = $user->getSetting('location');
        $socials = $user->getSetting('socials');
        $flag_reasons = FlagReason::enabled()->get();
        $feed_items = $user->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest()
            ->paginate(self::PAGINATION_COUNT)
            ->toJson();
        if ($user->id === AuthHelper::myId()) {
            return redirect()->route('front.my_profile');
        }

        return view('front.another-profile')->with(
            [
            'user'             => $user,
            'videos'           => $user->videos->sortByDesc('created_at')->take(3),
            'last_user_avatar' => $user->avatars->last(),
            'feed_items'       => $feed_items,
            'user_location'    => $location,
            'socials'          => collect($socials)->toJson(),
            'flag_reasons'     => $flag_reasons,
            ]
        );
    }

    public function getUserFeedPosts(Request $request)
    {
        $user_id = AuthHelper::myId();
        $user = User::findOrFail($user_id);
        $page_num = $request->page;
        $feed_items = $user->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest()
            ->paginate(self::PAGINATION_COUNT, ['*'], 'page', $page_num)
            ->toJson();

        return $feed_items;
    }

    public function settings()
    {
        $user = AuthHelper::me();
        $socials = $user->getSetting('socials');
        $flag_reasons = FlagReason::enabled()->get();
        $feed_items = $user->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest('pivot_updated_at')
            ->paginate(self::PAGINATION_COUNT)
            ->toJson();

        return view(
            'front.profile-settings',
            [
                'me'            => $user,
                'videos'        => $user->videos->sortByDesc('created_at')->take(3),
                'last_avatar'   => $user->avatars->last(),
                'feed_items'    => $feed_items,
                'user_location' => $user->getSetting('location'),
                'socials'       => $socials,
                'flag_reasons'  => $flag_reasons,
            ]
        );
    }

    public function donations()
    {
        $user = Auth::user();
        $socials = $user->getSetting('socials');
        $flag_reasons = FlagReason::enabled()->get();
        $feed_items = $user->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest('pivot_updated_at')
            ->paginate(self::PAGINATION_COUNT)
            ->toJson();

        return view(
            'front.profile-donations',
            [
                'me'            => $user,
                'videos'        => $user->videos->sortByDesc('created_at')->take(3),
                'last_avatar'   => $user->avatars->last(),
                'feed_items'    => $feed_items,
                'user_location' => $user->getSetting('location'),
                'socials'       => $socials,
                'flag_reasons'  => $flag_reasons
            ]
        );
    }
}
