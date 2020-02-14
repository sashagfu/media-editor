@extends('admin.layouts.app')

@section('title', trans('admin.dashboard'))

@section('content_header')
    <h1>{{ trans('reports.users_title') }}</h1>
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
                <th>{{trans('reports.reason')}}</th>
                <th>{{trans('reports.amount')}}</th>
                <th class="text-right">{{trans('admin.actions')}}</th>
            </tr>
            </thead>
            <tbody>
            <?php $reports = $__data['reports.users']?>
            @forelse($reports->groupBy('reportable_id') as $user_reports )
                @foreach ($user_reports->groupBy('reason') as $report)
                    <tr>
                        <td>{{$report->first()->reportable->id}}</td>
                        <td><a href="{{route('users.edit', $report->first()->reportable->id)}}">{{ $report->first()->reportable->username }}</a></td>
                        <td>{{$report->first()->reportable->email}}</td>
                        <td>{{$report->first()->reportable->created_at->diffForHumans()}}</td>
                        <td>{{ trans("reports." . $report->first()->reason) }}</td>
                        <td>{{ count($report)  }}</td>
                        <td class="text-right">
                            @include('admin.partials.list_actions', ['route' => 'users', 'id' => $report->first()->reportable->id])
                        </td>
                    </tr>
                    @endforeach
            @empty
                <tr><td colspan="5" class="text-center text-muted"><i class="fa fa-eye-slash"></i>&nbsp;{{trans('admin.no_items')}}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
    @include('admin.partials.list_footer', ['items' => $reports])
@endsection
