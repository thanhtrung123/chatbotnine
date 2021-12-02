@extends('layouts.admin')
@section('pageTitle', __('admin.header.variant').__('admin.import'))
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
                                <div class="col-md-12">
                                    @if(Session::has('msg_err_empty'))
                                        <div class="alert alert-danger">
                                            <p>{{Session::get('msg_err_empty')}}</p>
                                        </div>
                                    @endif
                                    {{ Form::form_csv_error('admin.variant_errors') }}
                                    {{ Form::form_error('excel') }}
                                    {{ Form::open(['url'=>route('admin.variant.import_store'), 'method'=>'POST','enctype'=>'multipart/form-data','id'=>'csv_import_form','class'=>'form-horizontal']) }}
                                    @if($isConfirm)
                                    <div class="row">
                                        <div class="col-md-12">
                                            {{__('admin.variant.notification_message')}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <a class="btn btn-warning btn-block" href="{{ route('admin.variant.import') }}">{{__('admin.cancel')}}</a>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="store" value="0">{{__('admin.register')}}</button>
                                        </div>
                                    </div>
                                    @else
                                    <div class="row bottom-buf10">
                                        <div class="col-md-12 text-danger">
                                            <strong>{{__('admin.variant.warning_message')}}</strong>
                                        </div>
                                    </div>
                                    {{ Form::form_file('excel',__('admin.excel_file'), '', ['accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel']) }}
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.import')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.variant.index') }}">{{__('admin.cancel')}}</a>
                                        </div>
                                    </div>
                                    @endif
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

@endsection
