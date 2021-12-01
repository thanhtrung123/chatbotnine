@extends('layouts.admin')
@section('pageTitle', __('admin.header.category').__('admin.edit'))
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
                                    {{ Form::open(['url'=>route('admin.category.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    {{ Form::form_text('name',__('admin.category.category_name'),true,['required'=>true,'autofocus'=>true]) }}

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.submit')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.category.index',['r'=>1]) }}">{{__('admin.cancel')}}</a>
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
