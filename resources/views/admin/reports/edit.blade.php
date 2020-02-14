@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('users.edit') }}</h1>
    @parent
@stop

@section('content')
    @if(isset($user))
        {{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put']) }}
        @include('admin.users._form', ['action' => 'Save'])
    @else
        {{ Form::open(['route' => 'users.store']) }}
        @include('admin.users._form', ['action' => 'Create User', 'user' => null])
    @endif
@endsection