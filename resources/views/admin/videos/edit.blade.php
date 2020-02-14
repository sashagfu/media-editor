@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('videos.edit') }}</h1>
    @parent
@stop

@section('content')
    @if(isset($video))
        {{ Form::model($video, ['route' => ['videos.update', $video->id], 'method' => 'put']) }}
        @include('admin.videos._form', ['action' => 'Save'])
    @else
        {{ Form::open(['route' => 'videos.store']) }}
        @include('admin.videos._form', ['action' => 'Create Video', 'video' => null])
    @endif
@endsection