<div style="white-space: nowrap;">
        {{ Form::open(['route' => [$route . '.destroy', $id], 'method' => 'delete', 'class' => 'btn-form-action-form']) }}
        <button type="submit" class="btn btn-flat btn-danger btn-xs btn-destroy btn-form-action" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></button>
        {{ Form::close() }}
</div>