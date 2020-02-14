<div class="box-header row">
    <div class="col-md-12 text-right">
        <div class="btn-group">
            @if(isset($item))
                {!! Form::button('<i class="fa fa-save"></i>&nbsp; ' . $action, [
                        'class'     => 'btn btn-success margin',
                        'type'      => 'submit',
                        'onclick'   => 'jQuery("input[name=action_mode]").val("update")'
                    ])
                !!}
                {!! Form::hidden('action_mode', 'update') !!}
            @endif
        </div>
    </div>
</div>
<!-- /.box-header -->
