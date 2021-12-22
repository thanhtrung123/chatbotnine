@extends('layouts.admin')
@section('pageTitle', __('学習データ').' インポート')
@section('cssfiles')
    <style type="text/css">
        .image_list_intersect {
            height: auto;
            max-height: 150px;
            overflow: auto
        }
    </style>
@endsection
@section('jsfiles')
    <script type="text/javascript">
        var MAX_SIZE_UPLOAD = "{{ config('wysiwyg.config.post_max_size') }}";
    </script>
    <script src="{{ asset(mix('js/custom.js')) }}"></script>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
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
                                        {{ Form::form_csv_error('admin.learning_errors') }}
                                        {{ Form::form_error('excel') }}
                                        {{ Form::form_error('zip') }}
                                        {{ Form::open(['url'=>route('admin.learning.import_store'), 'method'=>'POST','enctype'=>'multipart/form-data','id'=>'csv_import_form','class'=>'form-horizontal']) }}
                                        @if($isConfirm)
                                            {{ Form::hidden('confirmFile', old('confirmFile'))}}
                                            {{ Form::hidden('confirmZip', old('confirmZip'))}}
                                            {{ Form::hidden('path_name', old('path_name'))}}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    問題ございません。<br/>
                                                    このまま、{{__('学習データ')}}を更新される場合、以下の登録ボタンをクリックしてください。
                                                    @if(old('confirmZip') != null && count(old('image_list_intersect')) > 0)
                                                        <br/> 以下の画像ファイルは上書きされます。
                                                        <div class="image_list_intersect">
                                                            <ul>
                                                                @foreach(old('image_list_intersect') as $key => $value)
                                                                    <li> {{$value}} </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-2">
                                                    <a class="btn btn-warning btn-block" href="{{ route('admin.learning.import') }}">キャンセル</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <button class="btn btn-primary btn-block" type="submit" name="store" value="0">登録</button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row bottom-buf10">
                                                <div class="col-md-12 text-danger">
                                                    <strong>!!注意!! 現状の{{__('学習データ')}}はすべて削除され、画像データは上書きされます（復元はできません）</strong>
                                                </div>
                                            </div>
                                            {{ Form::form_file('excel','Excelファイル(.xlsx、.xls)', '', ['accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel']) }}
                                            {{ Form::form_file('zip','Zipファイル(.jpg, .png, .gifを圧縮したファイル)', '', ['accept' => '.zip']) }}
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-2">
                                                    <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">インポート</button>
                                                </div>
                                                <div class="col-md-2">
                                                    <a class="btn btn-default btn-block" href="{{ route('admin.learning.index') }}">戻る</a>
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