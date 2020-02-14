<?php

namespace App\Http\Controllers\Front;

use Auth;
use App\Models\Post;
use App\Models\Project as Video;
use App\Models\FlagReason;
use App\Models\Hashtag;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    const PAGINATION_COUNT = 10;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Performance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $p_posts = Post::performancePosts()
            ->skipFlagged($user)
            ->with('videos', 'images', 'author', 'stars')
            ->latest()
            ->paginate(self::PAGINATION_COUNT);

        $flag_reasons = FlagReason::enabled()->get();

        $videos = $user->projects
            ->load('stars')
            ->load('author')
            ->load('likes')
            ->map(function ($video) {
                $video->isPerformance = true;
                $video->file_path = 'http://tesla.r.acdnpro.com/media/1/5/v15521257524789_935.mp4';
                return $video;
            });

        return view('front.performance', compact('p_posts', 'user', 'flag_reasons', 'videos'));
    }

    public function getPerformancesPosts(Request $request)
    {
        $page_num = $request->page;
        $user = Auth::user();
        $feed_items = Post::performancePosts()
            ->skipFlagged($user)
            ->with('videos', 'images', 'author', 'stars')
            ->latest()
            ->paginate(self::PAGINATION_COUNT, ['*'], 'page', $page_num);

        return $feed_items;
    }
}
