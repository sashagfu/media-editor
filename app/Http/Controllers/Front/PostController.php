<?php

namespace App\Http\Controllers\Front;

use App\Helpers\DBHelper;
use App\Models\Image;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Post;
use App\Models\Video;
use App\Models\Circle;
use App\Models\User;
use App\Models\FlagReason;
use Session;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('singlePost');
    }

    public function singlePost($slug)
    {
        $user = AuthHelper::me();
        $post = Post::whereSlug($slug);
        if ($user) {
            $flagged = Post::flagged($user)->get()->contains($post->first()->id);
            if ($flagged) {
                return(redirect(route('front.my_profile')));
            }
        }

        $post = $post
            ->with('videos', 'images', 'comments', 'author', 'stars', 'likes')
            ->paginate(1);

        $flag_reasons = FlagReason::enabled()->get();
        if ($post->isEmpty()) {
            return redirect(route('front.my_profile'));
        } else {
            return view('front.post-single', compact('post', 'user', 'flag_reasons'));
        }
    }
}
