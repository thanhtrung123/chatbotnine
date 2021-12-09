<div class="form-group" data-confirm data-other-checkbox>
    <label class="col-md-4 control-label">{{__('admin.user.role_setting')}}</label>
    <div class="col-md-8">
        @foreach($roles as $role => $role_ary)
        @if(isset($readonly) && $readonly == true)
        @if($role_ary['checked'])
        <label class="checkbox-inline">{{ $role_ary['display_name'] }}</label>
        @endif
        @else
        <label class="checkbox-inline">
            <input type="hidden" value="0" name="{{ "roles[{$role}]" }}" />
            <input type="checkbox" value="1" name="{{ "roles[{$role}]" }}" {{ old("roles.{$role}",$role_ary['checked']) ? 'checked' : '' }} /> {{ $role_ary['display_name'] }}
        </label>
        @endif
        @endforeach
    </div>
</div>
<p>&nbsp;</p>