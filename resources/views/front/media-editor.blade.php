@extends('layouts.app')

@section('content_header')
    <h1>Video Editor</h1>
@stop

@section('content')
    <div class="me-bl-main">
        <div id="media-editor">
            <media-editor></media-editor>
        </div>
    </div>
    {{--<script src="/js/webgl-image-filter.js"></script>--}}
    <script src="{{ route('assets.lang') }}"></script>
    <script src="{{ mix('js/media-editor.js') }}"></script>
    <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
    {{--<script src="js/media-editor.js"></script> --}}{{-- will delete this --}}
@stop