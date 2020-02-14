<?php

namespace App\Http\Controllers\Front;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Auth;
use App\Models\Invite;
use App\Models\Circle;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index','welcome', 'circleInvite']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (Auth::guest()) {
            return view('front.feed');
        }

        return redirect()->route('feed.feed');
    }

    /**
     * Show welcome screen for new users.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        $user = AuthHelper::me();

        $invite = Invite::whereEmail($user->email)
            ->whereUsed(false)
            ->whereActive(true)
            ->first();

        if ($invite) {
            $circle_id = $invite->circle_id;
            $circle = Circle::find($circle_id);
            $redirect_link = route('circle.single', ['slug' => $circle->slug]);
            $invite->delete();
        }

        $redirect_link = isset($redirect_link) ? $redirect_link : route('feed.index');

        return view('welcome', compact('redirect_link'));
    }
}
