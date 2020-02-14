@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('users.title') }}</h1>
    @parent
@stop

@section('content')
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{trans('users.ID')}}</th>
                <th>{{trans('users.username')}}</th>
                <th>{{trans('users.email')}}</th>
                <th>{{trans('users.registered_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td><a href="{{route('users.edit', $user->id)}}">{{ $user->username }}</a></td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.partials.list_actions', ['route' => 'users', 'id' => $user->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
    @include('admin.partials.list_footer', ['items' => $users])
@endsection
