<?php

namespace App\Http\Controllers\Front;

use App\Models\FlagReason;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Post;

class FeedController extends Controller
{
    const PAGINATION_COUNT = 10;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the following page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = AuthHelper::me();
        $posts = Post::followingPosts($user)
            ->skipFlagged($user)
            ->with('videos', 'images', 'comments', 'author', 'stars', 'likes')
            ->latest()
            ->paginate(self::PAGINATION_COUNT);
        $flag_reasons = FlagReason::enabled()->get();

        return view('front.feed', compact('posts', 'user', 'flag_reasons'));
    }

    public function getPerformancesPosts(Request $request)
    {
        $page_num = $request->page;
        $user = Auth::user();
        $feed_items = Post::followingPosts($user)
            ->skipFlagged($user)
            ->with('videos', 'images', 'comments', 'author', 'stars')
            ->latest()
            ->paginate(self::PAGINATION_COUNT, ['*'], 'page', $page_num);

        return $feed_items;
    }
}
