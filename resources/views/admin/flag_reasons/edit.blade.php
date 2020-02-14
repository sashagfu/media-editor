@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('flag_reasons.edit') }}</h1>
    @parent
@stop

@section('content')
    @if(isset($flag_reason))
        {{ Form::model($flag_reason, ['route' => ['flag_reasons.update', $flag_reason->id], 'method' => 'put']) }}
        @include('admin.flag_reasons._form', ['action' => trans('flag_reasons.save')])
    @else
        {{ Form::open(['route' => 'flag_reasons.store']) }}
        @include('admin.flag_reasons._form', ['action' => trans('flag_reasons.update'), 'flag_reason' => null])
    @endif
@endsection