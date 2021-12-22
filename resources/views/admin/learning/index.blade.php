@extends('layouts.admin')
@section('pageTitle', __('学習データ').' 一覧')
@section('content')
@section('cssfiles')
    <link rel="stylesheet" href="{{ asset(mix('css/learning.css')) }}" type="text/css">
@endsection
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
                                @if(config('bot.truth.enabled'))
                                    {{ Form::form_text('keyword_key_phrase',__('キーフレーズ'),false,[]) }}
                                @endif
                                {{ Form::form_select('category_id','カテゴリ', $category_data,false,['class'=>'select2']) }}
                                {{--                                {{ Form::form_text('keyword_meta','キーワード(メタ)',false,[]) }}--}}
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
                                @if(auth()->user()->can('learning create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.create') }}">新規追加</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">新規追加</a>
                                @endif
                                @if(auth()->user()->can('learning import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.import') }}">インポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">インポート</a>
                                @endif
                                @if(auth()->user()->can('learning export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.export') }}">エクスポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">エクスポート</a>
                                @endif
                                @php
                                    $chain = [];
                                    $chain[] = route('api.admin.learning.sync',['mode'=>'delete']);
                                    $chain[] = route('api.admin.learning.sync',['mode'=>'add']);
                                    $ajax_modal = [
                                        'type' => 'ajax',
                                        'ajax' => ['url' => route('api.admin.learning.sync',['mode'=>'morph'])],
                                        'chain' => $chain,
                                        'message' => [
                                            'title' => __('学習データ').'同期',
                                            'body' => 'チャットボットAPIに'.__('学習データ').'を反映させます。<br>よろしければ、実行ボタンを押下してください。',
                                        ],
                                    ];
                                @endphp
                                <a id='sysn' class="btn btn-block {{ ($count_learning > 0) ? 'button-glow' : 'btn-default' }}" data-modal='@json($ajax_modal)'>同期</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table data-table" id="dtable_learning" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.learning.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="api_id">ID</th>
                                        <th data-name="question">質問文章</th>
                                        <th data-name="answer">回答文章</th>
                                        {{--                                        <th data-name="question_morph">解析後(仮</th>--}}
                                        {{--                                        <th data-name="metadata">メタ(仮</th>--}}
                                        @if(config('bot.truth.enabled'))
                                            <th data-name="key_phrase">キーフレーズ</th>
                                        @endif
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('learning edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.learning.edit',['user'=>'%id%']) }}">修正</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">修正</a>
                                                @endif
                                                @if(auth()->user()->can('learning destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.learning.destroy',['learning'=>'%id%'])]
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
