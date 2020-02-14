<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Circle;
use App\Models\Image;
use App\Models\FlagReason;

class CirclesController extends Controller
{
    const PAGINATION_COUNT = 10;
    const FEED_PAGINATION_COUNT = 10;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $circles = Circle::exceptSecret()->latest()->paginate(self::PAGINATION_COUNT);

        return view(
            'front.circles',
            with(
                [
                'circles' => $circles,
                ]
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
            'title'       => 'required|min:3',
            'description' => 'required|min:3|max:300',
            'type'        => 'required',
            'cover'       => 'image|max:2000',
            ]
        );

        $cover = $request->cover;

        $circle = Circle::create($request->all());

        if ($cover) {
            $image = Image::newCircleCover($cover, $circle->id);
            $circle->covers->add($image);
        }

        return route('circle.single', ['slug' => $circle->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $user = AuthHelper::me();
        $circle = Circle::whereSlug($slug)->firstOrFail();
        $feed_items = $circle->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest()
            ->paginate(self::PAGINATION_COUNT);
        $flag_reasons = FlagReason::enabled()->get();
        $requests = $circle->requestingUsers()->latest()->get();


        if ($user->can('view', $circle)) {
            return view('front.circle.single', compact('circle', 'feed_items', 'user', 'flag_reasons', 'requests'));
        } else {
            return redirect(route('circles.index'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $slug
     * @return \Illuminate\Http\Response
     */
    public function settings($slug)
    {
        $circle = Circle::whereSlug($slug)->firstOrFail();
        $user = AuthHelper::me();

        if ($user->can('updateSettings', $circle)) {
            return view('front.circle.settings', compact('circle'));
        } else {
            return redirect(route('circle.single', ['slug' => $slug]));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $this->validate(
            $request,
            [
            'title'                 => 'required|min:3',
            'description'           => 'required|min:3|max:300',
            'type'                  => 'required',
            'cover'                 => 'mimes:jpeg,jpg,png|max:2000',
            'post_adding_privacy'   => 'required',
            'members_block_privacy' => 'required',
            ]
        );

        $cover = $request->cover;

        $circle = Circle::whereSlug($slug)->firstOrFail();

        $circle->update($request->all());


        if ($cover) {
            $image = Image::newCircleCover($cover, $circle->id);
            $circle->covers->add($image);
        }

        return redirect(route('circle.settings', ['slug' => $slug]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function members($slug)
    {
        $circle = Circle::whereSlug($slug)->firstOrFail();
        $user = AuthHelper::me();
        $members = $circle->members()->latest()->paginate(self::PAGINATION_COUNT);

        if ($user->can('seeMembers', $circle)) {
            return view('front.circle.members', compact('circle', 'members'));
        } else {
            return redirect(route('circle.single', ['slug' => $slug]));
        }
    }

    public function getCirclesPosts($slug, Request $request)
    {
        $user = AuthHelper::me();
        $page_num = $request->page;
        $circle = Circle::whereSlug($slug)->firstOrFail();
        $feed_items = $circle->feed()
            ->with('author', 'comments', 'likes', 'stars')
            ->skipFlagged($user)
            ->latest()
            ->paginate(self::PAGINATION_COUNT, ['*'], 'page', $page_num)
            ->toJson();

        return $feed_items;
    }

    public function getCircles(Request $request)
    {
        $page_num = $request->page;

        $circles = Circle::exceptSecret()->latest()
            ->paginate(self::PAGINATION_COUNT, ['*'], 'page', $page_num);

        return $circles;
    }
}
