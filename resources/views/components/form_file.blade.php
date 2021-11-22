<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}" {{ $isConfirm ? 'data-confirm' : '' }}>
    <label for="name" class="col-md-4 control-label">{{ $label }}</label>
    <div class="col-md-8">
        {{ Form::file($name,$attr) }}
    </div>
</div>