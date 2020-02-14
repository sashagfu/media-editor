@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('videos.page_title') }}</h1>
    @parent
@stop

@section('content')
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{trans('videos.ID')}}</th>
                <th>{{trans('videos.thumbnail')}}</th>
                <th>{{trans('videos.author')}}</th>
                <th class="text-center">{{trans('videos.type')}}</th>
                <th>{{trans('videos.published_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($videos as $video)
                <tr>
                    <td>{{$video->id}}</td>
                    <td><a href="{{route('videos.edit', $video->id)}}"><img width="50" src="{{ $video->thumbnail_path }}" alt="" class="src"></a></td>
                    <td>{{$video->author->username}}</td>
                    <td class="text-center text-yellow">
                        @if($video->is_performance)<i class="fa fa-star" title="{{trans('videos.is_performance')}}"></i>@endif

                    </td>
                    <td>{{$video->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.partials.list_actions', ['route' => 'videos', 'id' => $video->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->

    @include('admin.partials.list_footer', ['items' => $videos])
@endsection
