<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}" {{ $isConfirm ? 'data-confirm' : '' }}>
    <label for="name" class="col-md-4 control-label">{{ $label }}</label>
    <div class="col-md-8">
        @foreach($option as $key => $val)
        <label class="radio-inline">
            {{ Form::radio($name,$key,$loop->first?true:false,$attr) }} {{ $val }}
        </label>
        @endforeach
        {{ Form::form_line_error($name) }}
    </div>
</div>