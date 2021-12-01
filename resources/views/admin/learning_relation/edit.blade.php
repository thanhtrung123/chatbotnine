@extends('layouts.admin')
@section('pageTitle', __('admin.header.related_questions').__('admin.edit'))
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
                                    {{ Form::open(['url'=>route('admin.learning_relation.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    {{ Form::form_text('id','関連質問ID',true,['readonly'=>'']) }}
                                    {{ Form::form_text('name', __('admin.learning_relation.related_question_name'), true, ['required'=>true, 'autofocus'=>true]) }}
                                    {{ Form::form_text('api_id', 'API_ID', true, ['required'=>true]) }}
                                    {{ Form::form_text('relation_api_id', __('admin.learning_relation.relation_api_id'), true, ['required'=>true]) }}
                                    {{ Form::form_text('order',  __('admin.learning_relation.display_order'), true, ['required'=>false,]) }}

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{ __('admin.submit')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.learning_relation.index',['r'=>1]) }}">{{ __('admin.cancel')}}</a>
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
