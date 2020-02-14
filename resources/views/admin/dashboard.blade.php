@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    @yield('content', '<p>Welcome to this beautiful admin panel.</p>')
@stop