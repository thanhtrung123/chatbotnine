@extends('layouts.admin')
@section('pageTitle', __('admin.header.関連質問').__('admin.登録')) 
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
                                    {{ Form::open(['url'=>route('admin.learning_relation.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    {{ Form::form_text('name', __('admin.learning_relation.関連質問名'), true, ['required'=>true, 'autofocus'=>true]) }}
                                    {{ Form::form_text('api_id', 'API_ID', true, ['required'=>true]) }}
                                    {{ Form::form_text('relation_api_id', __('admin.learning_relation.関連API_ID'), true, ['required'=>true]) }}
                                    {{ Form::form_text('order', __('admin.learning_relation.表示順'), true, ['required'=>false,]) }}

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.learning_relation.index') }}">{{__('admin.戻る')}}</a>
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
