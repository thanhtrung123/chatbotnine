@extends('layouts.admin')
@section('pageTitle', __('admin.header.異表記データ').__('admin.インポート'))
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
                                            {{__('admin.variant.問題ございません。<br/>このまま、異表記データを更新される場合、<br/>以下の登録ボタンをクリックしてください')}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <a class="btn btn-warning btn-block" href="{{ route('admin.variant.import') }}">{{__('admin.キャンセル')}}</a>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="store" value="0">{{__('admin.登録')}}</button>
                                        </div>
                                    </div>
                                    @else
                                    <div class="row bottom-buf10">
                                        <div class="col-md-12 text-danger">
                                            <strong>{{__('admin.variant.!!注意!! 現状の異表記データはすべて削除されます（復元はできません）')}}</strong>
                                        </div>
                                    </div>
                                    {{ Form::form_file('excel',__('admin.Excelファイル(.xlsx、.xls)'), '', ['accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel']) }}
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.インポート')}}</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-default btn-block" href="{{ route('admin.variant.index') }}">{{__('admin.戻る')}}</a>
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
