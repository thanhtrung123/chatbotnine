@if(session($name))
<div class="alert alert-danger">
    @foreach(session($name) as $idx => $row)
    <div class="row">
        @if($idx != 'invalid_format' && $idx != 'image')
            <div class="col-md-3"><strong>{{ $idx }}行目</strong></div>
        @endif
        <div class="col-md-9">
            @foreach($row as $col => $msgs)
            @foreach($msgs as $key => $val)
            <p>{{ $val }}</p>
            @endforeach
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif