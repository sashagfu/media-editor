@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    @if($searchQuery)
        <div class="pull-right"><a class="btn btn-link text-yellow" href="{{route('flags.index')}}">{{trans('flags.all_flags')}}</a></div>
    @endif

    <h1>
        {{ trans('flags.page_title') }}
        @if($searchQuery)
            {{trans('common.for')}}
            <a href="{{route('posts.edit', ['id' => $post->id])}}">{{$post->title}}</a>
        @endif
    </h1>
    @parent
@stop

@section('content')
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{trans('flags.ID')}}</th>
                @if(!$searchQuery)
                <th>{{trans('flags.flagged_item')}}</th>
                @endif
                <th>{{trans('flags.flagged_by')}}</th>
                <th>{{trans('flags.description')}}</th>
                <th>{{trans('flags.flagged_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($flags as $flag)
                <tr class="@if(!$flag->is_verified) bg-danger @endif">
                    <td>{{$flag->id}}</td>
                    @if(!$searchQuery)
                    <td><a href="{{route(str_plural($flag->flaggable_type) . '.edit', ['id' => $flag->id])}}">{{$flag->flaggable->title}}</a></td>
                    @endif
                    <td><a href="{{route('users.edit', ['id' => $flag->author->id])}}">{{$flag->author->username}}</a></td>
                    <td>{{str_limit($flag->description)}}</td>
                    <td>{{$flag->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.flags.list_actions', ['route' => 'flags', 'id' => $flag->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->

    @include('admin.partials.list_footer', ['items' => $flags])
@endsection
