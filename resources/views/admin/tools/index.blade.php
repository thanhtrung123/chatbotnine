@extends('layouts.admin')
@section('pageTitle', '裏ツール')
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

                                        <ul>
                                            <li><a href="{{ route('admin.tools.api') }}">API[{{config('bot.api.use')}}] データ確認</a></li>
                                            <li><a href="{{ route('admin.tools.stop_word') }}">ストップワード</a></li>
                                            <li><a href="{{ route('admin.tools.scenario') }}">シナリオ</a></li>
                                            <li><a href="{{ route('admin.tools.related_answer') }}">関連する回答</a></li>
                                            @if(config('bot.truth.enabled'))
                                                <li><a href="{{ route('admin.tools.truth_action') }}">真理表(操作)</a></li>
                                                <li><a href="{{ route('admin.tools.truth') }}">学習データ(真理表リアルタイム変換)一覧</a></li>
                                                <li><a href="{{ route('admin.tools.truth_db') }}">真理表(DB)一覧</a></li>
                                                <li><a href="{{ route('admin.tools.truth_morph') }}">真理表(変換)</a></li>
                                            @endif
                                            <li><a href="{{ route('admin.tools.common') }}">その他</a></li>
                                        </ul>

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
