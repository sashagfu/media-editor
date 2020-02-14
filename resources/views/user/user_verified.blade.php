
@extends('layouts.app')

@section('content')
    <div class="row align-center">
        <div class="small-9 align-center columns">
            <h3 class="text-center">{{trans('auth.user_verified')}}</h3>
            <div class="text-center">
                <i class="fa fa-smile-o fa-5x"></i>
            </div>
            <a href="{{route('following.index')}}">Go to my Actionlime</a>
        </div>
    </div>
@endsection