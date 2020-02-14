@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('posts.edit') }}</h1>
    @parent
@stop

@section('content')
    @if(isset($post))
        {{ Form::model($post, ['route' => ['posts.update', $post->id], 'method' => 'put']) }}
        @include('admin.posts._form', ['action' => 'Save'])
    @else
        {{ Form::open(['route' => 'posts.store']) }}
        @include('admin.posts._form', ['action' => 'Create Post', 'post' => null])
    @endif
@endsection