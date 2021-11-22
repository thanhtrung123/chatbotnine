@extends('layouts.enquete')
@section('pageTitle','アンケートエラー')

@section('content')

    <div id="wrapper-chatbot-ank">
        <div id="secton-header">
            <div class="container">
                <h1><span>アンケート</span></h1>
            </div>
        </div>
        <div id="main-content">
            <div class="container">
                <div class="" style="color: darkred;font-size: 128%;">
                    {!! $message !!}
                    @if(!$is_web)
                        <p>チャット画面に戻るには、本画面を閉じてください。</p>
                    @endif
                    <p></p>
                </div>
                @if($is_web)
                    <form class="form-horizontal" id="form_questionCheck" onsubmit="return false;">
                        <div class="btn-submit-question">
                            <p class="submit_question">
                                <button type="submit" id="close">OK</button>
                            </p>
                        </div>
                    </form>
                @endif

            </div>
        </div>
        <div id="footer">
            <div class="copy-right">
                <div class="container">
                    <p>{{config('bot.const.bot_copyright')}}</p>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script_body')
    <script>
        $(function () {
            $('#close').click(function () {
                window.close();
            });
        });
    </script>
@endsection
