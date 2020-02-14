@extends('layouts.app')

@section('content')
    @if(Auth::user())
        <project-statistics-page
                id="project-statistics-page"
                :project="{{$project->toJson()}}"
                :user="{{$user}}"
        ></project-statistics-page>
    @else
        <project-statistics-page
                id="project-statistics-page"
                :project="{{$project->toJson()}}"
        ></project-statistics-page>
    @endif
@endsection
