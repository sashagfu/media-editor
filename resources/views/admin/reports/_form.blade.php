@include('admin.partials.edit_table_header', ['action' => $action, 'item' => $user])

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
        {!! Form::label('username') !!}
        {!! Form::text('username', old('username'), [
            'class'         => 'form-control',
            'placeholder'   => trans('users.username'),
            'autofocus'     => true,
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email') !!}
        {!! Form::text('email', old('email'), [
            'class'         => 'form-control',
            'placeholder'   => trans('users.email'),
            'autofocus'     => false,
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('password') !!}
        {!! Form::password('password', [
            'class'         => 'form-control',
            'placeholder'   => trans('users.password'),
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('talent') !!}
        {!! Form::text('talent', old('talent'), [
            'class'         => 'form-control',
            'placeholder'   => trans('users.talent'),
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('display_name') !!}
        {!! Form::text('display_name', old('display_name'), [
            'class'         => 'form-control',
            'placeholder'   => trans('users.display_name'),
            'autocomplete'  => 'off',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('quote') !!}
        {!! Form::textarea('quote', old('quote'), [
            'class'         => 'form-control',
            'placeholder'   => trans('users.quote'),
        ]) !!}
    </div>
</div>
<!-- /.box-body -->
