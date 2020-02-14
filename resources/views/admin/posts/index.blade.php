@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('posts.page_title') }}</h1>
    @parent
@stop

@section('content')
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{trans('posts.ID')}}</th>
                <th>{{trans('posts.title')}}</th>
                <th>{{trans('posts.description')}}</th>
                <th>{{trans('posts.media')}}</th>
                <th>{{trans('posts.reactions')}}</th>
                <th>{{trans('common.created_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td>{{$post->id}}</td>
                    <td><a href="{{route('posts.edit', $post->id)}}">{{$post->title}}</a></td>
                    <td>{{str_limit($post->description)}}</td>
                    <td><a href="{{route('videos.edit', $post->media->id)}}"><img width="50" src="{{$post->media->thumbnail_path}}"></a></td>
                    <td>
                        @if($post->media->is_performance)
                            <div><a href="{{route('posts.stars', ['id' => $post->id])}}" class="text-yellow" title="{{trans('posts.stars')}}"><i class="fa fa-star"></i>&nbsp;{{$post->stars->count()}}</a></div>
                        @else
                            <div><a href="{{route('posts.likes', ['id' => $post->id])}}" class="text-yellow" title="{{trans('posts.likes')}}"><i class="fa fa-thumbs-up"></i>&nbsp;{{$post->likes->count()}}</a></div>
                        @endif
                        <div><a href="{{route('posts.comments', ['id' => $post->id])}}" title="{{trans('posts.comments')}}" class="text-green"><i class="fa fa-comment"></i>&nbsp;{{$post->comments->count()}}</a></div>
                        <div><a href="{{route('posts.flags', ['id' => $post->id])}}" title="{{trans('posts.flags')}}" class="text-red"><i class="fa fa-flag"></i>&nbsp;{{ $post->flaggable->count() }}</a></div>
                    </td>
                    <td style="white-space: nowrap;">{{$post->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.partials.list_actions', ['route' => 'posts', 'id' => $post->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->

    @include('admin.partials.list_footer', ['items' => $posts])
@endsection
