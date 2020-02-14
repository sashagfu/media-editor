@extends('adminlte::page')

@push('css')
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
@endpush

@push('js')
    <script src="{{ mix('js/admin.js') }}"></script>
    @if (Session::has('message'))
        <script>
            toastr.{{ Session::get('alert-class') }}('{{ addslashes(Session::get('message')) }}');
        </script>
    @endif
@endpush
