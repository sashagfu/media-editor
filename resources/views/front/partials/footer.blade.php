@if(Auth::check())
<chat-widget id="chat-widget"></chat-widget>
{{--<footer class="footer">--}}
    {{--<div class="container">--}}
        {{--<div class="content has-text-centered">--}}
            {{--<p class="main-footer__slogan">{{trans('common.yuustar_subhead')}}</p>--}}
            {{--<p class="main-footer__copyright">{{trans('common.yuustar')}} © 2016</p>--}}
            {{--<div class="label radius text-right">Env: {{$env}}</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</footer>--}}
<!--Notification-->
<audio id="notificationSound" class="notification-sound hide" src="{{route('api.notification.sound')}}" type="audio/ogg"></audio>
@endif