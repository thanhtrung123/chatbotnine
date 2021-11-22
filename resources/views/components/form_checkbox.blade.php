<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}" {{ $isConfirm ? 'data-confirm' : '' }}>
    <label for="name" class="col-md-4 control-label">{{ $label }}</label>
    <div class="col-md-8">
        @if(count($option) == 1)
            <input type="hidden" name="{{$name}}" value="0">
        @endif
        @foreach($option as $key => $val)
            <label class="checkbox-inline">
                {{ Form::checkbox($name,$key,null,$attr) }} {{ $val }}
            </label>
        @endforeach
        {{ Form::form_line_error($name) }}
    </div>
</div>