@extends('layouts.app')
@section('content')
    <home-page id="homepage"
               :login-url="'{{url(config('adminlte.login_url', 'login'))}}'"
               :register-url="'{{ url(config('adminlte.register_url', 'register')) }}'"
               :csrf-token="'{{csrf_token()}}'"
               >
    </home-page>
@stop