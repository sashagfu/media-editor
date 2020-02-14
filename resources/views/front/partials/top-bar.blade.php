{{--@if(Auth::check())--}}
    {{--<nav class="nav top-bar-navigation">--}}
        {{--<div class="nav-left">--}}
            {{--<a class="nav-item" href="{{ url('/') }}">--}}
                {{--{{ config('app.name', 'Laravel') }}--}}
            {{--</a>--}}
            {{--@if (Auth::check())--}}
                {{--<a class="nav-item top-bar-search ui-widget">--}}
                    {{--<input type="text" class="input top-bar-search__input" placeholder="Search me">--}}
                    {{--<i class="top-bar-search__spinner is-hidden fa fa-spinner fa-2x fa-spin fa-fw"></i>--}}
                {{--</a>--}}
            {{--@endif--}}
        {{--</div>--}}
        {{--<div class="nav-right nav-menu" style="position: relative;">--}}
            {{--<a class="nav-item" href="{{route('following.index')}}" role="button">{{trans('common.following')}}</a>--}}
            {{--<a class="nav-item" href="{{route('performances.index')}}"--}}
               {{--role="button">{{trans('common.performances')}}</a>--}}
            {{--<a class="nav-item" href="{{route('circles.index')}}" role="button">{{trans('circles.name')}}</a>--}}
            {{--@if (Auth::guest())--}}
                {{--<a class="nav-item" href="{{ url('/login') }}">Login</a>--}}
                {{--<a class="nav-item" href="{{ url('/register') }}">Register</a>--}}
            {{--@else--}}
                {{--<a class="nav-item" href="{{route('front.my_profile')}}"--}}
                   {{--role="button">{{ '@' . Auth::user()->username }}</a>--}}
                {{--<a class="nav-item">--}}
                    {{--<top-chat-menu id="chat-top-menu"></top-chat-menu>--}}
                {{--</a>--}}
                {{--<a href="#" class="nav-item notifications-toggler" data-toggle="notifications-container">--}}
                    {{--<i class="fa fa-bell-o"></i>--}}
                    {{--@if(Auth::user()->unreadNotifications->count())--}}
                        {{--<span class="notifications-count badge">{{Auth::user()->unreadNotifications->count()}}</span>--}}
                    {{--@endif--}}
                {{--</a>--}}
                {{--<a href="#" tabindex="-1" class="nav-item"--}}
                   {{--onclick="event.preventDefault();--}}
                             {{--document.getElementById('logout-form').submit();">--}}
                    {{--{{ trans('auth.logout') }}--}}
                    {{--<form id="logout-form" action="{{ url('/logout') }}" method="POST"--}}
                          {{--style="display: none;">{{ csrf_field() }}</form>--}}
                {{--</a>--}}
            {{--@endif--}}
            {{--@if (Auth::check())--}}
                {{--<div class="callout notifications-container is-hidden" data-toggler=".hide"--}}
                     {{--id="notifications-container">--}}
                    {{--<div class="row notifications-container__row">--}}
                        {{--@if(!Auth::user()->notifications->count())--}}
                            {{--<p class="notifications-container__empty">{{trans('notifications.empty')}}</p>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--@endif--}}
        {{--</div>--}}
    {{--</nav>--}}
{{--@endif--}}