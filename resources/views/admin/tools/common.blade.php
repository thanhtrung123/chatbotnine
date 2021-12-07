@extends('layouts.admin')
@section('pageTitle', '裏ツール その他')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

                <div class="row">
                    <div class="col-md-12">

                        <div class="panel panel-default">
                            <div class="panel-heading">コンソールコマンド系</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1">

                                        <div class="panel panel-default">
                                            <div class="panel-heading">全キャッシュクリア</div>
                                            <div class="panel-body">
                                                clear-compiled<br> cache:clear<br> config:clear<br> view:clear<br> route:clear<br> debugbar:clear<br>
                                                上記コマンドを実行します。
                                            </div>
                                            <div class="panel-footer"><a class="btn btn-default" href="{{ route('admin.tools.common',['mode'=>'cache_clear']) }}">実行</a></div>
                                        </div>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">メンテナンスモード</div>
                                            <div class="panel-body">
                                                メンテナンスモードに切り替えます。<br>解除するには[artisan up]を実行してください。
                                            </div>
                                            @php
                                                $confirm = ['message'=>"メンテナンスモードに切り替えると、全ページメンテナンス中となります。\n解除にはコンソールでの操作が必要なので注意してください。"];
                                            @endphp
                                            <div class="panel-footer"><a class="btn btn-default" href="{{ route('admin.tools.common',['mode'=>'maintenance']) }}" data-button-confirm='@json($confirm)'>{{__('admin.execution')}}</a></div>
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
