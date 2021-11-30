@extends('layouts.admin')
@section('pageTitle', __('admin.header.ログ情報').__('admin.一覧'))
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
                                    {{ Form::open(['class'=>'form-horizontal','id'=>'search_form']) }}
                                    <!--{{ Form::form_text('keyword','キーワード',false,['autofocus'=>true]) }}-->
                                    {{ Form::form_text('date',__('admin.log.日付'),false,['class'=>'datepicker']) }}
                                    {{ Form::form_select('processing',__('admin.log.処理'),[''=>'']+$processing,false,['class'=>'select2']) }}
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.検索')}}</button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.log.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                   <thead>
                                    <tr>
                                        <th data-name="action_datetime" data-format="datetime">{{__('admin.log.日時')}}</th>
                                        <th data-name="user_id">{{__('admin.log.ユーザーID')}}</th>
                                        <th data-name="user_name">{{__('admin.log.ユーザー名')}}</th>
                                        <th data-name="user_role">{{__('admin.log.ロール')}}</th>
                                        <th data-name="processing">{{__('admin.log.操作')}}</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
