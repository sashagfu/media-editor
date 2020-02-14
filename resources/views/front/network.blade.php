@extends('layouts.app')
@section('content')
    {{--<app id="app"></app>--}}
    <networking-page
        id="networking-page"
        :user="{{$user}}"
    ></networking-page>
@endsection
