@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    @if($searchQuery)
        <div class="pull-right"><a class="btn btn-link text-yellow" href="{{route('flag_reasons.index')}}">{{trans('flag_reasons.all_flag_reasons')}}</a></div>
    @endif

    <h1>
        {{ trans('flag_reasons.page_title') }}
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
                <th>{{trans('flag_reasons.ID')}}</th>
                <th>{{trans('flag_reasons.title')}}</th>
                <th>{{trans('flag_reasons.enabled')}}</th>
                <th>{{trans('flag_reasons.created_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($flag_reasons as $flag_reason)
                <tr class="@if(!$flag_reason->enabled) bg-warning @endif">
                    <td>{{$flag_reason->id}}</td>
                    <td><a href="{{route('flag_reasons.edit', ['id' => $flag_reason->id])}}">{{$flag_reason->title}}</a></td>
                    <td>@if($flag_reason->enabled)<i class="fa fa-check-circle-o text-success" title="{{trans('flag_reasons.enabled')}}"></i>@endif</td>
                    <td>{{$flag_reason->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.partials.list_actions', ['route' => 'flag_reasons', 'id' => $flag_reason->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->

    @include('admin.partials.list_footer', ['items' => $flag_reasons])
@endsection
