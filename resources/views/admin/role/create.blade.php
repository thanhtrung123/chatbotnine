@extends('layouts.admin')
@section('pageTitle', __('admin.header.権限情報').__('admin.追加'))
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>@yield('pageTitle')</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-10">
                                    {{ Form::open(['url'=>route('admin.role.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    {{ Form::form_text('name',__('admin.roles.ロール名'),true,['required'=>true,'autofocus'=>true]) }}
                                    {{ Form::form_text('display_name',__('admin.roles.表示名'),true,['required'=>true]) }}

                                    @include('admin.role.permission')

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.role.index') }}">{{__('admin.戻る')}}</a>
                                        </div>
                                    </div>

                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($isConfirm)
{{ Form::form_confirm_script('entry_form')  }}
@endif
@endsection
