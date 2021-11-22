@foreach($permissions as $resource => $resource_ary)
<hr>
<div class="form-group" data-confirm data-other-checkbox>
    <label class="col-md-4 control-label">{{ __($resource_ary['display_name']) }}</label>
    <div class="col-md-8">
        @foreach($resource_ary['privileges'] as $privilege => $privilege_ary)
        @if(isset($readonly) && $readonly == true)
        @if($privilege_ary['checked'])
        <label class="checkbox-inline">{{ $privilege_ary['display_name'] }}</label> 
        @endif
        @else
        <label class="checkbox-inline">
            <input type="hidden" value="0" name="{{ "permission[{$resource}:{$privilege}]" }}" >
            <input type="checkbox" value="1" name="{{ "permission[{$resource}:{$privilege}]" }}" {{ old("permission.{$resource}:{$privilege}",$privilege_ary['checked']) ? 'checked' : '' }}> {{ $privilege_ary['display_name'] }}
        </label> 
        @endif
        @endforeach
    </div>
</div>
@endforeach
<p>&nbsp;</p>