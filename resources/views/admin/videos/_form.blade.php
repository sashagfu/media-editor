@include('admin.partials.edit_table_header', ['action' => $action, 'item' => $video])

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
        {!! Form::label('file_path') !!}
        {!! Form::text('file_path', old('file_path'), [
            'class'         => 'form-control',
            'placeholder'   => trans('videos.file_path'),
            'autofocus'     => true,
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('thumbnail_path') !!}
        {!! Form::text('thumbnail_path', old('thumbnail_path'), [
            'class'         => 'form-control',
            'placeholder'   => trans('videos.thumbnail_path'),
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('author_id') !!}
        {!! Form::select('author_id', $authors, old('author'), [
            'class'         => 'form-control',
        ]) !!}
    </div>

    <div class="form-group">

        {!! Form::checkbox('is_performance', 1, old('is_performance')) !!}
        {!! Form::label('is_performance') !!}
    </div>
</div>
<!-- /.box-body -->

