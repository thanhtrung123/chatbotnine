@extends('layouts.admin')
@section('pageTitle', __('アカウント情報') . ' 追加')
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
                                    {{ Form::open(['url'=>route('admin.user.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    {{ Form::form_text('name','ログインID',true,['required'=>true,'autofocus'=>true]) }}
                                    {{ Form::form_text('display_name','表示名',true,['required'=>true]) }}
                                    {{ Form::form_email('email','メールアドレス',true,['required'=>true]) }}
                                    {{ Form::form_password('password','パスワード',['required'=>true]) }}
                                    {{ Form::form_password('password_confirmation','パスワード確認',['required'=>true]) }}

                                     @include('admin.user.roles')
                                    
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">確認</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.user.index') }}">戻る</a>
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
