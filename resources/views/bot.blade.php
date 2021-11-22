@extends('layouts.bot')
@section('pageTitle', __(config('bot.const.bot_title')))
@php
    $voice_flg = config('bot.speech.enabled');
    $voice_api = env('API_SPEECH_ENABLE', false);
@endphp
@section('content')
    <div id="wrapper">
        <div id="header">
            <div class="container">
                <h1>
                    {{__(config('bot.const.bot_title'))}}
                    @if(!empty(auth()->user()) && $disp_info )
                        <span style="font-size: 70%;">
{{--                        &nbsp;({{__('管理者モード')}})&nbsp;--}}
                        <label>情報メッセージを表示する<input type="checkbox" id="disp_info" checked></label>
                    </span>
                    @endif
                </h1>
                <p id="request">
                    <a href="javascript:void(0);" id="bot_reset_btn">{{__(config('bot.const.bot_symbol_reset'))}}</a>
                </p>
            </div>
        </div>
        <div id="main-content">
            <div class="container">
                <div id="msg_area" class="messages">
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="message_input">
                <form action="javascript:void(0);" class="frm-message" id="chat_form">
                    <div class="voice_btn">
                        @if ($voice_flg == TRUE AND $voice_api == TRUE)
                            <a id="voice_btn_disabled" class="stop" href="javascript:void(0);">
                                <i class="fa fa-microphone-slash" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                    <input id="txt_input" type="text" class="text" placeholder="{{__('質問を入力してください')}}" required autocomplete="off" data-suggest='@json(['url'=>route('api.bot.suggest'),'wait'=>500])' />
                    <button type="submit" class="btn-submit" id="button_submit"> 送信 </button>
                </form>
            </div>
        </div>
    </div>
    {{--  テンプレート：ユーザーメッセージ  --}}
    <template id="tpl_msg_user">
        <div class="message-container text-right self-message">
            <div class="message">
                <div class="cnt">{msg}</div>
            </div>
        </div>
    </template>
    {{--  テンプレート：ボットメッセージ  --}}
    <template id="tpl_msg_bot">
        <div class="message-container text-left bot-message">
            <div class="avatar">
                <p><img src="{{asset('img/images/avatar.png')}}" alt="avatar"></p>
            </div>
            <div class="message">
                <div class="cnt">{msg}{select_q}
                    {select_btn}
                </div>
            </div>
        </div>
    </template>
    {{--  テンプレート：選択ボタン（デフォルト）  --}}
    <template id="tpl_sel_def_btn_list">
        <li class="{btn_cls}"><a href="javascript:void(0);" class="sel_btn sel_def_btn" data-status="{status}" data-symbol="{symbol}" data-option="{option}">{message}</a></li>
    </template>
    {{--  テンプレート：選択ボタン（リンク）  --}}
    <template id="tpl_sel_def_link_list">
        <li class="col-6"><a href="{href}" target="{target}" class="" data-option="{option}">{message}</a></li>
    </template>
    {{--  テンプレート：選択ボタンエリア（デフォルト）  --}}
    <template id="tpl_sel_def_btn">
        <ul class="answers mt15 sp_mt5 {answer_type}">{select_btn_list}</ul>
    </template>
    {{--  テンプレート：ボット画像メッセージ  --}}
    <template id="tpl_msg_img">
        <div class="message-container text-left bot-message image-message">
            <div class="message">
                <div class="cnt"><img src="{img_path}"></div>
            </div>
        </div>
    </template>

    <script>
        window.CHAT_BOT = {
            id: '{{ $chat_id }}',
            bot_const: @json(output_config_json('bot.const')),
            init_data: @json($init_data),
            route: @json($route),
            ua_status: @json(config('const.useragent.status')),
        };
    </script>
    @if ($voice_flg == TRUE AND $voice_api == TRUE && $browser_support_flg == TRUE)
        <script>
            const OPTIONS = {
                AUDIO: {
                    BUFFER_SIZE: 2048,
                    LANG: 'ja-JP',
                },
                WAVE_VIEW: {
                    FLAG: true,
                    FILL_STYLE: 'rgb(16, 16, 24)',
                    STROKE_STYLE: 'rgb(124, 224, 124)',
                },
                TIME_OUT : {{ config('bot.speech.timeout') }}
            }
            var voiceRecog;
            var timeout_id, transcript = '';
            var max_time_out = {{ config('bot.speech.during') }};
            var url_upload = "{{ route('speech.upload') }}";
            var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        </script>
        <script src="{{ asset(mix('js/voiceRecognition.js')) }}"></script>
    @endif
@endsection
