<div style="white-space: nowrap;">
    <a href="{{route($route . '.edit', $id)}}" class="btn btn-flat btn-warning btn-xs btn-form-action"><i class="fa fa-edit"></i></a>&nbsp;
    {{ Form::open(['route' => [$route . '.destroy', $id], 'method' => 'delete', 'class' => 'btn-form-action-form']) }}
    <button type="submit" class="btn btn-flat btn-danger btn-xs btn-destroy btn-form-action" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></button>
    {{ Form::close() }}
</div>