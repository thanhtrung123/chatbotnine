@extends('layouts.admin')
@section('pageTitle', __('ログ情報').' 一覧')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>

                <div class="row">
                    <div class="col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1">
                                        {{ Form::open(['class'=>'form-horizontal','id'=>'search_form']) }}
                                        {{ Form::form_text('date_s','日付(開始)',false,['class'=>'datepicker']) }}
                                        {{ Form::form_text('date_e','日付(終了)',false,['class'=>'datepicker']) }}
                                        {{ Form::form_checkbox('feedback[]','フィードバック',$checkbox['feedback'],false) }}
                                        {{ Form::form_checkbox('status[]','検索対象',$checkbox['status'],false) }}
                                        <div class="row">
                                            <div class="col-md-12" id="accordion">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading"><a href="#accordion_1" data-toggle="collapse" data-parent="#accordion">詳細検索</a></div>
                                                    <div id="accordion_1" class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            {{ Form::form_checkbox('keyword_columns[]','検索対象',$checkbox['keyword'],false) }}
                                                            {{ Form::form_text('keyword','キーワード',false,['autofocus'=>true]) }}
                                                            {{-- Form::form_select('score_s','スコア(最小)',$score,false,[]) --}}
                                                            {{-- Form::form_select('score_e','スコア(最大)',$score,false,[]) --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


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
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body">
{{--                                @if(auth()->user()->can('response_info export'))--}}
                                    <a class="btn btn-block btn-default" href="{{ route('admin.response_info.export') }}">エクスポート</a>
{{--                                @else--}}
{{--                                    <a class="btn btn-block btn-default" disabled="">エクスポート</a>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @php
                                    $dtable = [
                                        'ajax'=>[ 'url' => route('api.admin.response_info.list') ],
                                        'setting' =>['stateSave'=>true,'order' => [[1,'desc']]],
                                        'form' => ['search_form'],
                                    ];
                                    $details = [
                                         'cache' => true,
                                         'data' => [],
                                    ];
                                    if(config('bot.truth.enabled')){
                                        $details['data'][] = [
                                            'ajax' => ['url'=>route('api.admin.response_info.truth_detail')],
                                            'params' => [],
                                            'columns' => ['id'],
                                            'header' => ['yes_word'=>'「はい」を選択','no_word'=>'「いいえ」を選択'],
                                           
                                        ];
                                    }
                                    $details['data'][] = [
                                        'ajax' => ['url'=>route('api.admin.response_info.detail')],
                                        'params' => [],
                                        'columns' => ['id'],
                                        'header' => ['status'=>'ステータス','api_question'=>'質問','api_answer'=>'回答','api_score'=>'スコア'],
                                    ];
                                @endphp

                                <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json($dtable)'>
                                    <thead>
                                    <tr>
                                        <th data-detail='@json($details)'></th>
                                        <th data-name="action_datetime" data-format="datetime">日時</th>
                                        <th data-name="user_ip">ユーザIP</th>
                                        <th data-name="status">ステータス</th>
                                        <th data-name="user_input">ユーザ入力値</th>
                                        <!--<th data-name="api_question">質問</th>-->
                                        <th data-name="api_answer">回答</th>
                                        <th data-name="api_score">スコア</th>
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
