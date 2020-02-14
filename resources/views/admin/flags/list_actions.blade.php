<div style="white-space: nowrap;">
        @if(!$flag->is_verified)
            {{ Form::open(['route' => [$route . '.verify', $id], 'method' => 'patch', 'class' => 'btn-form-action-form']) }}
                <button type="submit" class="btn btn-flat btn-primary btn-xs btn-form-action"><i class="fa fa-check-circle-o" title="{{trans('flags.verify')}}"></i></button>&nbsp;
            {{ Form::close() }}
        @endif
        {{ Form::open(['route' => [$route . '.destroy', $id], 'method' => 'delete', 'class' => 'btn-form-action-form']) }}
        <button type="submit" class="btn btn-flat btn-danger btn-xs btn-destroy btn-form-action" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></button>
        {{ Form::close() }}
</div>