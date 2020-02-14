@extends('layouts.app')
@section('content_header')
    <h1>Profile</h1>
@stop
@section('content')
    <profile-page id="profilePage"
                  :items="{{$feed_items}}"
                  :user="{{$me}}"
                  :form-type="'user'"
                  :show-form="true"
                  :flag-reasons="{{$flag_reasons}}"
                  :socials="{{$socials}}"
                  :videos="{{$videos}}"
                  :gmap-key="'{{env('GOOGLE_MAPS_API_KEY')}}'"
                  :location="{{$user_location}}"
    >
    </profile-page>
@stop
