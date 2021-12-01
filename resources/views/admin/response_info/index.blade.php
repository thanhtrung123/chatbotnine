@extends('layouts.admin')
@section('pageTitle', __('admin.header.Response_stt').__('admin.list'))
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
                                        {{ Form::form_text('date_s',__('admin.response_info.date_start'),false,['class'=>'datepicker']) }}
                                        {{ Form::form_text('date_e',__('admin.response_info.date_end'),false,['class'=>'datepicker']) }}
                                        {{ Form::form_checkbox('feedback[]',__('admin.response_info.feedback'),$checkbox['feedback'],false) }}
                                        {{ Form::form_checkbox('status[]',__('admin.response_info.status'),$checkbox['status'],false) }}
                                        <div class="row">
                                            <div class="col-md-12" id="accordion">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading"><a href="#accordion_1" data-toggle="collapse" data-parent="#accordion">{{__('admin.response_info.search_target')}}</a></div>
                                                    <div id="accordion_1" class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            {{ Form::form_checkbox('keyword_columns[]',__('admin.response_info.search_target'),$checkbox['keyword'],false) }}
                                                            {{ Form::form_text('keyword',__('admin.keyword'),false,['autofocus'=>true]) }}
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
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.search')}}</button>
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
                                    <a class="btn btn-block btn-default" href="{{ route('admin.response_info.export') }}">{{__('admin.export')}}</a>
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
                                            'header' => ['yes_word'=>__('admin.response_info.select_yes'),'no_word'=>__('admin.response_info.select_no')],
                                           
                                        ];
                                    }
                                    $details['data'][] = [
                                        'ajax' => ['url'=>route('api.admin.response_info.detail')],
                                        'params' => [],
                                        'columns' => ['id'],
                                        'header' => ['status'=>__('admin.response_info.status'),'api_question'=>__('admin.response_info.question'),'api_answer'=>__('admin.response_info.answer'),'api_score'=>__('admin.response_info.score')],
                                    ];
                                @endphp

                                <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json($dtable)'>
                                    <thead>
                                    <tr>
                                        <th data-detail='@json($details)'></th>
                                        <th data-name="action_datetime" data-format="datetime">{{__('admin.response_info.date_time')}}</th>
                                        <th data-name="user_ip">{{__('admin.response_info.user_ip')}}</th>
                                        <th data-name="status">{{__('admin.response_info.status')}}</th>
                                        <th data-name="user_input">{{__('admin.response_info.user_input')}}</th>
                                        <!--<th data-name="api_question">質問</th>-->
                                        <th data-name="api_answer">{{__('admin.response_info.answer')}}</th>
                                        <th data-name="api_score">{{__('admin.response_info.score')}}</th>
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
