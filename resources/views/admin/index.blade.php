@extends('layouts.admin')
@section('pageTitle', '管理トップ')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading big">データ管理</div>
                            <div class="panel-body">
                                <div class="row mb-2 ">
                                    <div class="col-md-6">{{__("学習データ")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.learning.index') }}">登録情報確認</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("類義語データ")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.synonym.index') }}">登録情報確認</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("異表記データ")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.variant.index') }}">登録情報確認</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("固有名詞")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.proper_noun.index') }}">登録情報確認</a></div>
                                </div>
                                @if(config('bot.truth.enabled'))
                                    <div class="row top-buf5">
                                        <div class="col-md-6">{{__("キーフレーズ")}}</div>
                                        <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.key_phrase.index') }}">登録情報確認</a></div>
                                    </div>
                                @endif
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("カテゴリ")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.category.index') }}">登録情報確認</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("シナリオ管理")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.scenario.editor') }}">登録情報確認</a></div>
                                </div>
                                <div class="row top-buf5">
                                    <div class="col-md-6">{{__("関連質問")}}</div>
                                    <div class="col-md-6"><a class="btn btn-block btn-default" href="{{ route('admin.learning_relation.index') }}">登録情報確認</a></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">システム管理</div>
                            <div class="panel-body">
                                <a class="btn btn-block btn-default" href="{{ route('admin.user.index') }}">{{ __('アカウント情報') }}管理</a>
                                <a class="btn btn-block btn-default" href="{{ route('admin.role.index') }}">{{ __('権限情報') }}管理</a>
                                <a class="btn btn-block btn-default" href="{{ route('admin.log.index') }}">{{ __('ログ情報') }}管理</a>
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
                            <div class="panel-heading">応答状況管理</div>

                            <div class="panel-body">

                                <div class="row">

                                    <div class="col-md-2">
                                        <a class="btn btn-default" href="{{ route('admin.response_info.index') }}">応答状況確認</a>
                                    </div>
                                    <div class="col-md-2">
                                        <a class="btn btn-default" href="{{ route('admin.enquete.index') }}">アンケート確認</a>
                                    </div>
                                    <div class="col-md-2">
                                        <a class="btn btn-default" href="{{ route('admin.report.list') }}">応答状況集計</a>
                                    </div>

                                </div>

                                <div class="row top-buf15">

                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">利用者数</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($user_count) }} 件
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">問い合わせ件数</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($question_count) }} 件
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">アンケート回答数</div>
                                            <div class="panel-body text-right">
                                                {{ number_format($enquete_count) }} 件
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
