<div class="row">
    @can('seeMembers', $circle)
        <p>{{trans('circles.recent-members')}}</p>
        @foreach($members as $user)
            <div class="row circle-members__user-wrapper">
                <div class="small-4 columns">
                    <a href="#"><img src="{{$user->avatar}}" class=" thumbnail"></a>
                </div>
                <div class="small-8 columns">
                    <a href="{{route('front.another_profile', ['username' => $user->username])}}">{{$user->display_name}}</a></h5>
                    <p>{{trans('profiles.talent')}}: {{$user->talent}}</p>
                </div>
            </div>
            <hr>
        @endforeach
    @endcan
</div>