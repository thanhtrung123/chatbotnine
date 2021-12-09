@extends('layouts.admin')
@section('pageTitle', __('admin.index.top_management'))
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading big">{{__('admin.index.top_management')}}</div>
                            <div class="panel-body">
                                <div class="row mb-2 ">
                                    <div class="col-md-6">{{__("admin.header.data_management")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.learning.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.synonym_data")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.synonym.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.variant")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.variant.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.proper_noun")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.proper_noun.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                @if(config('bot.truth.enabled'))
                                    <div class="row top-buf5">
                                        <div class="col-md-6">{{__("admin.header.key_phrase")}}</div>
                                        <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.key_phrase.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                    </div>
                                @endif
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.category")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.category.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.scenario_management")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.scenario.editor') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("admin.header.related_questions")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.learning_relation.index') }}">{{__('admin.index.confirm_inf')}}</a></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{__("admin.header.system_management")}}</div>
                            <div class="panel-body">
                                <a class="btn btn-block btn-default" href="{{ route('admin.user.index') }}">{{ __('admin.header.account_inf') }} {{__('admin.header.management')}}</a>
                                <a class="btn btn-block btn-default" href="{{ route('admin.role.index') }}">{{ __('admin.header.authority_inf') }} {{__('admin.header.management')}}</a>
                                <a class="btn btn-block btn-default" href="{{ route('admin.log.index') }}">{{ __('admin.header.log') }} {{__('admin.header.management')}}</a>
                            </div>
                        </div>
                    </div>
                    {{--                <div class="col-md-4">--}}
                    {{--                    <div class="panel panel-default">--}}
                    {{--                        <div class="panel-heading">お知らせ</div>--}}
                    {{--                        <div class="panel-body">--}}
                    {{--                           <ul>--}}
                    {{--                               <li><b>TITLE</b> 内容…</li>--}}
                    {{--                               <li><b>TITLE</b> 内容…</li>--}}
                    {{--                               <li><b>TITLE</b> 内容…</li>--}}
                    {{--                           </ul>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{ __('admin.index.res_stt_management')}}</div>

                            <div class="panel-body">

                                <div class="row">

                                    <div class="col-md-3">
                                        <a class="btn btn-default" href="{{ route('admin.response_info.index') }}">{{ __('admin.index.check_res_stt') }}</a>
                                    </div>
                                    <div class="col-md-3">
                                        <a class="btn btn-default" href="{{ route('admin.enquete.index') }}">{{ __('admin.index.questionnaire_confirm') }}</a>
                                    </div>
                                    <div class="col-md-3">
                                        <a class="btn btn-default" href="{{ route('admin.report.list') }}">{{ __('admin.index.res_stt_summary') }}</a>
                                    </div>

                                </div>

                                <div class="row top-buf15">

                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">{{ __('admin.index.users') }}</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($user_count) }} {{ __("admin.index.subject") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">{{ __('admin.index.inquiries') }}</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($question_count) }} {{ __('admin.index.log_inf') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">{{ __('admin.index.questionnaire_res') }}</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($enquete_count) }} {{ __('admin.index.subject') }}
                                            </div>
                                        </div>
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
