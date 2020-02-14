<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Actionlime') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/front.css') }}" rel="stylesheet">
    {{--<link href="/css/front.css" rel="stylesheet">--}}

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken'            => csrf_token(),
            'apiUrl'               => route('index'),
            'awsKey'               => env('AWS_ACCESS_KEY_ID', 'aws_key'),
            'awsBucket'            => env('AWS_ACCESS_MEDIA_BUCKET', 'aws_bucket'),
            'awsRegion'            => env('AWS_REGION', 'us-east-1'),
            'awsSignUrl'           => route('videos.aws_sign_key'),
            'user_id'              => Auth::id(),
            'broadcast_driver'     => env('BROADCAST_DRIVER'),
            'broadcast_key'        => env('ECHO_KEY'),
            'broadcast_host'       => env('ECHO_HOST'),
            'broadcast_port'       => env('ECHO_PORT'),
            'recaptcha_site_key'   => env('RECAPTCHA_SITE_KEY'),
            'pusher_key'       => env('PUSHER_KEY'),
            'pusher_cluster'       => env('PUSHER_CLUSTER'),
        ]); ?>
    </script>
</head>
<body class="site">
    @stack('socials-sharing')
    @include('front.partials.top-bar')
    <main class="site-content">
        @yield('content')
    </main>
    {{--@if(Auth::user())--}}
        {{--<chat-widget id="chat-widget"></chat-widget>--}}
    {{--@endif --}}
    @include('front.partials.footer')
    <!-- Scripts -->
    <script src="{{ route('assets.lang') }}"></script>
    <script src="{{ mix('js/front.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>
    {{--<script src="/js/front.js"></script>--}}
    @stack('footer-js')
</body>
</html>
