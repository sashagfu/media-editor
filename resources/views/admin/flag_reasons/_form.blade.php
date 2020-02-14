@include('admin.partials.edit_table_header', ['action' => $action, 'item' => $flag_reason])

<!-- /.box-header -->
<div class="box-body">
    <!-- if there are creation errors, they will show here -->
    @if(count($errors) > 0)
        <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Html::ul($errors->all()) }}
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('title') !!}
        {!! Form::text('title', old('title'), [
            'class'         => 'form-control',
            'placeholder'   => trans('flag_reasons.title'),
            'autofocus'     => true,
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">

        {!! Form::checkbox('enabled', 1, old('enabled')) !!}
        {!! Form::label('enabled') !!}
    </div>
</div>
<!-- /.box-body -->
