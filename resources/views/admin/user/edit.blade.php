@extends('layouts.admin')
@section('pageTitle', __('admin.header.アカウント情報').__('admin.修正'))
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
                                        {{ Form::open(['url'=>route('admin.user.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}

                                        {{ Form::form_text('name',__('admin.user.ログインID'),true,['required'=>true,'autofocus'=>true,'readonly'=>(request('name') == config('acl.admin.user'))]) }}
                                        {{ Form::form_text('display_name',__('admin.user.表示名'),true,['required'=>true]) }}
                                        {{ Form::form_email('email',__('admin.user.メールアドレス'),true,['required'=>true]) }}
                                        {{ Form::form_password('password',__('admin.user.パスワード'),[]) }}
                                        {{ Form::form_password('password_confirmation',__('admin.user.パスワード確認'),[]) }}

                                        @include('admin.user.roles')

                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.user.show',['user'=>$id]) }}">{{__('admin.戻る')}}</a>
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
        {{ Form::form_confirm_script('entry_form','update')  }}
    @endif
@endsection
