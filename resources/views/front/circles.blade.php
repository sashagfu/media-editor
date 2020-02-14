@extends('layouts.app')

@section('content')
    <circles-page id="circles-page"
                  :circles="{{$circles->toJson()}}"
                    >

    </circles-page>
@endsection