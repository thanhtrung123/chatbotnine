@php
    $name2 = a2d($name);
@endphp
@if ($errors->has($name))
    <span class="help-block">
    <strong>{{ $errors->first($name) }}</strong>
</span>
@elseif($errors->has($name2))
    <span class="help-block">
    <strong>{{ $errors->first($name2) }}</strong>
</span>
@endif