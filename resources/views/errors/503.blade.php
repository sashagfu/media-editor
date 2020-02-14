<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="description" content="Actionlime - social network for creative people">

    <title>{{ config('app.name', 'Actionlime') }}</title>

    <link href="{{ mix('css/maintenance.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="images/star-icon.png">

</head>

<body>
<!-- LOADER TEMPLATE -->
<div id="page-loader">
    <div class="loader-icon fa fa-spin colored-border"></div>
</div>
<!-- /LOADER TEMPLATE -->

<!-- HEADER -->
<header id="top">
    <canvas id="constellation"></canvas>

    <!-- YOUTUBE PLAYER -->
    <div id="video" data-video="rLbeo-n8SJs" data-mute="true"></div>
    <!-- /YOUTUBE PLAYER -->

    <div class="welcome">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-8">

                        <div class="logo"><img width="200" src="images/actionime-logo.png" alt="Actionlime"></div>
                        <div class="typed">Our website is under construction.</div>
                        <p>We are working very hard to give you the best experience with this one.<br>You will love
                            Actionlime as much as we do. It will morph perfectly on your needs!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER -->

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row">
            <div class="copyright">Copyright © {{ date('Y') }} Actionlime. All rights reserved.</div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->
<script src="{{ mix('js/maintenance.js') }}"></script>
</body>
</html>