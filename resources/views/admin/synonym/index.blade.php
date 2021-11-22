@extends('layouts.admin')
@section('pageTitle', __('類義語データ').' 一覧')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>

                <div class="row">
                    <div class="col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ Form::open(['class'=>'form-horizontal','id'=>'search_form']) }}
                                {{ Form::form_text('keyword','キーワード',false,['autofocus'=>true]) }}
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">検索</button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @if(auth()->user()->can('synonym create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.create') }}">新規追加</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">新規追加</a>
                                @endif
                                @if(auth()->user()->can('synonym import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.import') }}">インポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">インポート</a>
                                @endif
                                @if(auth()->user()->can('synonym export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.export') }}">エクスポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">エクスポート</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table data-table" id="dtable_synonym" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.synonym.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="keyword">類義語文字</th>
                                        <th data-name="synonym">置換後文字</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('synonym edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.synonym.edit',['user'=>'%id%']) }}">修正</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">修正</a>
                                                @endif
                                                @if(auth()->user()->can('synonym destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.synonym.destroy',['synonym'=>'%id%'])]
                                                ])'>削除</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">削除</a>
                                                @endif
                                            </template>
                                        </th>
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
