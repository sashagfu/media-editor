<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Models\Post;
use App\Models\FlagReason;

class HashtagController extends Controller
{
    const PAGINATION_COUNT = 10;

    public function hashtagPosts($hashtag_name)
    {
        $user = Auth::user();

        $posts = Post::where('content', 'like', '%'.'#'.$hashtag_name.'%')
            ->select('posts.*')
            ->skipFlagged($user)
            ->with('videos', 'images', 'author', 'stars')
            ->latest()
            ->paginate(self::PAGINATION_COUNT);

        $flag_reasons = FlagReason::enabled()->get();

        return view('front.posts-hashtags', compact('posts', 'user', 'flag_reasons'));
    }
}
