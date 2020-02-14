@include('admin.partials.edit_table_header', ['action' => $action, 'item' => $post])

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
            'placeholder'   => trans('videos.title'),
            'autofocus'     => true,
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('description') !!}
        {!! Form::textarea('description', old('description'), [
            'class'         => 'form-control',
            'placeholder'   => trans('videos.description'),
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('media_id') !!}
        {!! Form::select('media_id', $media, old('media_id'), [
            'class'         => 'form-control',
        ]) !!}
    </div>

</div>
<!-- /.box-body -->
