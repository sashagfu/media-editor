@extends('layouts.app')

@section('content_header')
    <h1>Video Editor</h1>
@stop

@section('content')
    <div class="me-bl-main">
        <div class="me-bl-main__row columns is-marginless">
            <div class="me-bl-main__column column is-12">
                <div class="me-bl-main-tale">
                    <div class="me-bl-main-tale__left">
                        <div class="me-bl-main-tale__title">
                            Media editor
                        </div>
                    </div>
                    <div class="me-bl-main-tale__right">
                        <div class="me-bl-main-tale__set"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="media-editor"></div>
    </div>
    <script src="/js/webgl-image-filter.js"></script>

    <script src="{{ mix('js/media-editor-react.js') }}"></script>
    {{--<script src="/js/media-editor-react.js"></script>--}}
@stop