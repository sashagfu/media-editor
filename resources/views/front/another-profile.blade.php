@push('socials-sharing')
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=1018288364968233";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<script>window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));</script>
@endpush

@extends('layouts.app')

@section('content_header')
    <h1>Profile</h1>
@stop

@section('content')
   <profile-page id="profilePage"
                  :items="{{$feed_items}}"
                  :user="{{$user}}"
                  :flag-reasons="{{$flag_reasons}}"
                  :socials="{{collect($socials)->toJson()}}"
                  :videos="{{$videos}}"
                  :gmap-key="'{{env('GOOGLE_MAPS_API_KEY')}}'"
                  :location="{{$user_location}}"
    >
    </profile-page>
@stop
@push('footer-js')
<script>const user = {!! $user !!}</script>
@endpush
