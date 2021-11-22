<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}" {{ $isConfirm ? 'data-confirm' : '' }}>
    <label for="name" class="col-md-4 control-label">{{ $label }}</label>
    <div class="col-md-8">
        @php($attr['class'] = isset($attr['class']) ? $attr['class'].' form-control' : 'form-control')
        {{ Form::select($name,$option,null,$attr) }}
        {{ Form::form_line_error($name) }}
    </div>
</div>