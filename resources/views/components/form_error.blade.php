@if ($errors->has($name))
    <div class="alert alert-danger">
        <p>{{ $errors->first($name) }} </p>
    </div>
@endif