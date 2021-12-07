@extends('layouts.admin')
@section('pageTitle', __('admin.header.sesponse_stt_summary'))
@section('cssfiles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset(mix('css/dashboard.css')) }}" rel="stylesheet">
@stop
@section('jsfiles')
<script src="{{ asset(mix('js/chart.js')) }}"></script>
<script type="text/javascript">
        @if (count($ip) > 0)
        {!! "var ip = ".json_encode($ip)  !!}
        @else
            var ip = null;
        @endif
    @if (($answer_state_data['count_answer'] > 0 OR $answer_state_data['count_no_answer'] > 0) AND $answer_state_data['count_answer'] > 0)
        var flag_answer = 1;
        var flag_no_answer = 1;
        {!! "var data_answer = ". json_encode([$answer_state_data['count_answer'], $answer_state_data['count_no_answer']]) . "" !!};
        {!! "var data_handle = ". json_encode([$answer_state_data['count_answer_handle'], $answer_state_data['count_answer_no_handle'],  $answer_state_data['count_answer_yet_handle']]) . "" !!};
    @elseif($answer_state_data['count_answer'] > 0 OR $answer_state_data['count_no_answer'] > 0)
        var flag_answer = 1;
        var flag_no_answer = 0;
        {!! "var data_answer = ". json_encode([$answer_state_data['count_answer'], $answer_state_data['count_no_answer']]) . "" !!};
        var data_handle = []
    @elseif($answer_state_data['count_answer'] > 0)
        var flag_answer = 0;
        {!! "var data_handle = ". json_encode([$answer_state_data['count_answer_handle'], $answer_state_data['count_answer_no_handle'],  $answer_state_data['count_answer_yet_handle']]) . "" !!};
    @else
        var flag_answer = 0;
        var flag_no_answer = 0;
        var data_answer = [];
        var data_handle = [];
    @endif
    @if (count($enquete_answer_data) > 0)
        var flag_enquete = 1;
        {!! "var enquete_combine = ". json_encode($enquete_answer_data) . "" !!};
    @else
        var flag_enquete = 0;
        var enquete_combine = [];
    @endif
    {!! "var url_export = '". route('admin.report.export') . "'" !!}
    {!! "var url_search = '". route('admin.report.list') . "'" !!}
    {!! "var data_date = ". json_encode($state_uses_data['date']) . "" !!}
    {!! "var data_result = ". json_encode($state_uses_data['data_result']) . "" !!}
    {!! "var hour = ". json_encode($state_uses_data['hour']) . "" !!}
    {!! "var data_hour = ". json_encode($state_uses_data['data_hour']) . "" !!}
    {!! "var day_of_week = ". json_encode($state_uses_data['day_of_week']) . "" !!}
    {!! "var data_day_of_week = ". json_encode($state_uses_data['data_day_of_week']) . "" !!}
    {!! "var data_talk = ". json_encode($state_uses_data['data_talk']) . "" !!}
    {!! "var data_talk_hour = ". json_encode($state_uses_data['data_talk_hour']) . "" !!}
    {!! "var data_talk_day_of_week = ". json_encode($state_uses_data['data_talk_day_of_week']) . "" !!}
    {!! "var data_answer = ". json_encode([$answer_state_data['count_answer'], $answer_state_data['count_no_answer']]) . "" !!}
    {!! "var data_handle = ". json_encode([$answer_state_data['count_answer_handle'], $answer_state_data['count_answer_no_handle'],  $answer_state_data['count_answer_yet_handle']]) . "" !!}
    {!! "var enquete_combine = ". json_encode($enquete_answer_data) . "" !!}
    {!! "var upload_url =  '". route('admin.report.upload'). "'" !!}
    
    if (ip != null) {
        $('.myContainer').TagsInput({
            tagInputPlaceholder:'',
            tagHiddenInput: $('.inputTags'),
            tagContainerBorderColor: '#d3d3d3',
            tagBackgroundColor: '#FFA500',
            tagColor: '#fff',
            tagBorderColor: '#FFA500',
            initialTags: ip
        });
    } else {
        $('.myContainer').TagsInput({
            tagInputPlaceholder:'',
            tagHiddenInput: $('.inputTags'),
            tagContainerBorderColor: '#d3d3d3',
            tagBackgroundColor: '#FFA500',
            tagColor: '#fff',
            tagBorderColor: '#FFA500'
        });
    }
    $(function () {
        // 詳細条件の開閉
        @if (($filters['channel'] ?? NULL) OR ($filters['ip'] ?? NULL))
            $("#detailed_conditions_target").css('display', 'block');
        @else
            $("#detailed_conditions_target").css('display', 'none');
        @endif
        $('#detailed_conditions').click(function() {
            $(this).toggleClass('open');
            $('#detailed_conditions_target').slideToggle();
        });
    });
</script>
@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.report.list') }}" accept-charset="UTF-8" id="form_dashboard" class="section-narrow" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">{{__('admin.report.specify_aggregation_conditions')}}</div>
                                <div class="panel-body">
                                    <div id="total_date" class="total_date_disp">
                                        <div class="ttl ttl_disp">
                                            <label for="date_s" class="control-label date_s_disp">{{__('admin.report.aggregation_period')}}</label>
                                        </div>
                                        <div class="cnt cnt_disp">
                                            <input type="text" name="date_s" class="datepicker form-control date_w_disp" autocomplete="off" value="{{ $start_date }}">
                                            <p class="date_m_disp">～</p>
                                            <input type="text" name="date_e" class="datepicker form-control date_w_disp" autocomplete="off" value="{{ $end_date }}">
                                        </div>
                                    </div>
                                    <div>
                                        <p id="detailed_conditions" class="accordion">{{__('admin.report.detail_conditions')}}</p>
                                        <div id="detailed_conditions_target">
                                            <div class="total_date_disp">
                                                <div class="ttl_disp">
                                                    <label for="date_s" class="control-label date_s_disp">{{__('admin.report.aggregate_exclusion_ip')}}</label>
                                                </div>
                                                <div class="cnt_disp">
                                                    @php
                                                        $channel_list = config('const.bot.channel');
                                                        $channel_ary = array();
                                                        foreach ($channel_list as $channel_data) {
                                                            $channel_ary[$channel_data['id']] = $channel_data['name'];
                                                        }
                                                    @endphp
                                                    {!! Form::select('channel', ['' => '指定なし'] + $channel_ary, '', ['class' => 'form-control ttl_disp']) !!}
                                                </div>
                                            </div>
                                            <div class="total_date_disp">
                                                <div class="ttl_disp">
                                                    <label for="date_s" class="control-label date_s_disp">{{__('admin.report.aggregate_exclusion_ip')}}</label>
                                                </div>
                                                <div class="cnt_disp">
                                                <div class="myContainer"></div>
                                                <input type="text" class="inputTags" name="ip" hidden/>
                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="aggregate_submit">
                                        <div class="ttl_disp"></div>
                                        <div class="aggregate_w_submit">
                                            <button type="submit" class="btn btn-primary btn-block" id="search_dashboard">{{__('admin.totalling')}}</button>
                                        </div>
                                        <div class="aggregate_e_submit">
                                            <button type='button' id="dashboard" class="btn btn-primary btn-block">{{__('admin.export')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{__('admin.report.usage_situation')}}</div>
                            <div class="panel-body">
                                <!-- タブ・メニュー -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_period" data-toggle="tab">{{__('admin.report.period')}}</a></li>
                                    <li><a href="#tab_time" data-toggle="tab">{{__('admin.report.time')}}</a></li>
                                    <li><a href="#tab_week" data-toggle="tab">{{__('admin.report.day_of_week')}}</a></li>
                                </ul>
                                <!-- タブ内容 -->
                                <div class="tab-content" id="tab_print">
                                    <!-- 期間 -->
                                    <div class="tab-pane active" id="tab_period">
                                        <div class="tab_pane_disp">
                                            <div class="periodChart">
                                                <canvas id="periodChart"></canvas>
                                            </div>
                                            <div class="statistics_disp">
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_users')}}</strong></p>
                                                    <p class="user_unique">{{__('admin.report.number_unique_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_unique'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                    <p class="user_unique">{{__('admin.report.total_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_date'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                </div>
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_talks')}}</strong></p>
                                                    <p class="user_talk"><strong>{{ $state_uses_data['total_user']['user_talk'] }}</strong></p>
                                                    <p class="user_talk_date">（{{__('admin.report.1会話平均：')}}{{ ($state_uses_data['total_user']['user_date'] > 0) ? round($state_uses_data['total_user']['user_talk']/$state_uses_data['total_user']['user_date'], 2) : 0 }}）</p>
                                                    <p class="user_talk_number">{{__('admin.report.subject')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 時間 -->
                                    <div class="tab-pane" id="tab_time">
                                        <div class="tab_pane_disp">
                                            <div class="tab-chart">
                                                <canvas id="timeChart"></canvas>
                                            </div> 
                                            <div class="statistics_disp">
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_users')}}</strong></p>
                                                    <p class="user_unique">{{__('admin.report.number_unique_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_unique'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                    <p class="user_unique">{{__('admin.report.total_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_hour'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                </div>
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_talks')}}</strong></p>
                                                    <p class="user_talk"><strong>{{ $state_uses_data['total_user']['user_talk_hour'] }}</strong></p>
                                                    <p class="user_talk_date">（{{__('admin.report.1会話平均：')}}{{ ($state_uses_data['total_user']['user_hour'] > 0) ? round($state_uses_data['total_user']['user_talk_hour']/$state_uses_data['total_user']['user_hour'], 2) : 0 }}）</p>
                                                    <p class="user_talk_number">{{__('admin.report.subject')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 曜日 -->
                                    <div class="tab-pane" id="tab_week">
                                        <div class="tab_pane_disp">
                                            <div class="tab-chart">
                                                <canvas id="weekChart"></canvas>
                                            </div>
                                            <div class="statistics_disp">
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_users')}}</strong></p>
                                                    <p class="user_unique">{{__('admin.report.number_unique_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_unique'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                    <p class="user_unique">{{__('admin.report.total_users')}}</p>
                                                    <p class="user_unique_number"><strong>{{ $state_uses_data['total_user']['user_day_of_week'] }}</strong></p>
                                                    <p class="user_number">{{__('admin.report.people')}}</p>
                                                </div>
                                                <div class="user_statistic">
                                                    <p class="user_total"><strong>{{__('admin.report.number_talks')}}</strong></p>
                                                    <p class="user_talk"><strong>{{ $state_uses_data['total_user']['user_talk_day_of_week'] }}</strong></p>
                                                    <p class="user_talk_date">（{{__('admin.report.1会話平均：')}}{{ ($state_uses_data['total_user']['user_day_of_week'] > 0) ? round($state_uses_data['total_user']['user_talk_day_of_week']/$state_uses_data['total_user']['user_day_of_week'], 2) : 0 }}）</p>
                                                    <p class="user_talk_number">{{__('admin.report.answer_status')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($answer_state_data['count_answer'] > 0 OR $answer_state_data['count_no_answer'] > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{__('admin.report.answer_status')}}</div>
                            <div class="panel-body">
                                <div class="print_chart print_chart_flex">
                                    <div class="chart_rate">
                                        <div class="chart_rate_response">
                                            <canvas id="responseRateChart"></canvas>
                                        </div>
                                        <div class="chart_rate_response_parent">
                                            <div class="chart_h_response">
                                                <p class="chart_rate_response_total"><strong>{{__('admin.report.number_res')}}</strong></p>
                                                <p class="chart_rate_response_number">{{__('admin.report.number_answers')}}</p>
                                                <p class="chart_rate_response_number_disp"><strong>{{ $answer_state_data['count_answer'] }}</strong></p>
                                                <p class="chart_rate_response_number_case">{{__('admin.report.subject')}}</p>
                                                <p class="chart_rate_response_number">{{__('admin.report.number_unanswered')}}</p>
                                                <p class="chart_rate_response_number_disp"><strong>{{ $answer_state_data['count_no_answer'] }}</strong></p>
                                                <p class="chart_rate_response_number_case">{{__('admin.report.subject')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($answer_state_data['count_answer'] > 0)
                                    <div  class="chart_rate">
                                        <div class="chart_rate_response">
                                            <canvas id="resolutionRateChart"></canvas>
                                        </div>
                                        <div class="chart_rate_response_parent">
                                            <div class="chart_h_response chart_h_responses">
                                                <p class="chart_rate_response_total"><strong>{{__('admin.report.number_resolutions')}}</strong></p>
                                                <p class="chart_rate_response_number">{{__('admin.report.number_answers')}}</p>
                                                <p class="chart_rate_response_number_disp"><strong>{{ $answer_state_data['count_answer_handle'] }}</strong></p>
                                                <p class="chart_rate_response_number_case">{{__('admin.report.subject')}}</p>
                                                <p class="chart_rate_response_number">{{__('admin.report.number_not_resolved')}}</p>
                                                <p class="chart_rate_response_number_disp"><strong>{{ $answer_state_data['count_answer_no_handle'] }}</strong></p>
                                                <p class="chart_rate_response_number_case">{{__('admin.report.subject')}}</p>
                                                <p class="chart_rate_response_number">{{__('admin.report.number_unanswered')}}</p>
                                                <p class="chart_rate_response_number_disp"><strong>{{ $answer_state_data['count_answer_yet_handle'] }}</strong></p>
                                                <p class="chart_rate_response_number_case">{{__('admin.report.subject')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if (count($answer_state_data['quest_popular_list']) > 0)
                                <div class="{{ (count($answer_state_data['quest_popular_list']) <= 10) ? 'result_answer' : 'result_answers'  }}">
                                    <div class="result_answer_disp">
                                        <p class="result_answer_title"><strong>{{__('admin.report.answered_period')}}</strong></p>
                                    </div>
                                    <div class="result_answer_disp_show">
                                        <ol class="data-list">
                                            @foreach ($answer_state_data['quest_popular_list'] as $quest_list)
                                                @php
                                                    $show_modal = [
                                                        'type' => 'showReport',
                                                        'message' => [
                                                            'category' =>  (object_get($quest_list, 'name')) ? object_get($quest_list, 'name') : '無し',
                                                            'question' => object_get($quest_list, 'question'),
                                                            'answer' => object_get($quest_list, 'answer')
                                                        ],
                                                    ];
                                                @endphp
                                            
                                            <li><a data-toggle="modal" data-target="#modalShow" class="modal_show" data-modal='@json($show_modal)'>{{ object_get($quest_list, 'question') }} ({{ object_get($quest_list, 'api') . '回' }})</a></li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                                @endif
                                @if (count($answer_state_data['quest_popular_no_list']) > 0)
                                <div class="{{ (count($answer_state_data['quest_popular_no_list']) <= 10) ? 'result_answer' : 'result_answers'  }}">
                                    <div class="result_answer_disp">
                                        <p class="result_answer_title"><strong>{{__('admin.report.Questions_could_not_ans')}}</strong></p>
                                    </div>
                                    <div class="result_answer_disp_show">
                                        <ol class="data-list">
                                            @foreach ($answer_state_data['quest_popular_no_list'] as $quest_list)
                                                <li>{{ object_get($quest_list, 'user_input') }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if (count($enquete_answer_data) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default" id="chart_table">
                            <div class="panel-heading test">{{__('admin.header.questionnaire')}}</div>
                            <div class="panel-body enquete-body">
                                    <div class="row-flex">
                                        @php
                                            $key = 0;
                                        @endphp
                                        @foreach ($enquete_answer_data as $key => $enquete_list)
                                            @php
                                                $key = $key + 1;
                                            @endphp
                                                <div class="col-md-6-flex enquete_result">
                                                    <div class="enquete_result_disp">
                                                        <p class="enquete_result_disp_name" alt="">{{ 'Q.' . $key .'　'. ($enquete_list['question_name'] ?? NULL) }}</p>
                                                    </div>
                                                    <div id="{{ 'enquete' . $key . 'ChartWrapper'}}" class="chart_wrapper clearfix">
                                                        <div class="enquete_result_disp_chart float-left">
                                                            <canvas id="{{ 'enquete' . $key . 'Chart'}}"></canvas>
                                                        </div>
                                                        <div class="float-left enquete_w_disp">
                                                                <div id="{{ 'js-legend' . $key}}" class="chart-legend">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        @endforeach
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <section class="surveyResults" id="one">
                    <div>
                        <canvas id="periodChart1"></canvas>
                        <canvas id="timeChart1"></canvas>
                        <canvas id="weekChart1"></canvas>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection