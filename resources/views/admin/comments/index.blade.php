@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    @if($searchQuery)
        <div class="pull-right"><a class="btn btn-link text-yellow" href="{{route('comments.index')}}">{{trans('comments.all_comments')}}</a></div>
    @endif

    <h1>
        {{ trans('comments.page_title') }}
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
                <th>{{trans('comments.text')}}</th>
                @if(!$searchQuery)
                <th>{{trans('comments.commented_item')}}</th>
                @endif
                <th>{{trans('comments.commented_by')}}</th>
                <th>{{trans('comments.commented_at')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($comments as $comment)
                <tr>
                    <td>{{str_limit($comment->text)}}</td>
                    @if(!$searchQuery)
                    <td><a href="{{route('posts.edit', ['id' => $comment->project->id])}}">{{$comment->project->title}}</a></td>
                    @endif
                    <td><a href="{{route('users.edit', ['id' => $comment->author->id])}}">{{$comment->author->username}}</a></td>
                    <td>{{$comment->created_at->diffForHumans()}}</td>
                    <td class="text-right">
                        @include('admin.comments.list_actions', ['route' => 'comments', 'id' => $comment->id])
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->

    @include('admin.partials.list_footer', ['items' => $comments])
@endsection
