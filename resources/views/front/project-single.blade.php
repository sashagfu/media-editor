@extends('layouts.app')

@section('content')
    @if(Auth::user())
        <single-project-page
                id="single-project-page"
                :project="{{$project->toJson()}}"
                :user="{{$user}}"
                :deprecated="{{$deprecated}}"
        ></single-project-page>
    @else
        <single-project-page
                id="single-project-page"
                :project="{{$project->toJson()}}"
                :deprecated="{{$deprecated}}"
        ></single-project-page>
    @endif
@endsection
