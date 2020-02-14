@extends('layouts/app')

@section('content')
    <verify-page id="verifyPage"
               :verify-link="'{{route('auth.verify_check', ['user_id' => $user->id])}}'"
               :csrf-token="'{{csrf_token()}}'"
    >
    </verify-page>
@stop