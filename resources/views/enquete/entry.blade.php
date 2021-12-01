@extends('layouts.enquete')
@section('pageTitle',$form_setting['title'])

@section('content')
    <div id="wrapper-chatbot-ank">
        <div id="secton-header">
            <div class="container">
                <h1><span>{{$form_setting['title']}}</span></h1>
            </div>
        </div>
        <div id="main-content">
            <div class="container">

                <div class="question-title">
                    {!! $form_setting['description'] !!}
                </div>

                {{ Form::open(['url'=>route('enquete.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'form_questionCheck']) }}

                @foreach($form_setting['items'] as $q_cd => $setting)
                    <div class="question-check">
                        <fieldset>
                            <legend>
                                <span class="question_txt">{{$form_setting['question_prefix']}}{{$loop->iteration}}</span>
                                <span class="question_name">{!! $setting['question'] !!}</span>
                                {!! $setting['remarks'] ?? '' !!}
                            </legend>
                            @switch($setting['type'])
                                @case(__('const.enquete.form.text.id'))
                                {{Form::text("question[{$q_cd}]")}}
                                @break
                                @case(config('const.enquete.form.textarea.id'))
                                <div class="question_textarea">
                                    <p>{{Form::textarea("question[{$q_cd}]",null,['placeholder'=>$setting['placeholder']??''])}}</p>
                                </div>
                                @break
                                @case(config('const.enquete.form.select.id'))
                                {{Form::select("question[{$q_cd}]",Constant::getConstArray($setting['items']))}}
                                @break
                                @case(config('const.enquete.form.checkbox.id'))
                                <div class="list_question">
                                    @foreach(Constant::getConstArray($setting['items']) as $key => $val)
                                        <p>
                                            {{Form::checkbox("question[{$q_cd}][]",$key,false,['id'=>"question_{$q_cd}_{$key}"])}}
                                            <label class="check-inline" for="{{"question_{$q_cd}_{$key}"}}">{{$val}}</label>
                                        </p>
                                    @endforeach
                                </div>
                                @break
                                @case(config('const.enquete.form.radio.id'))
                                <div class="list_question">
                                    @foreach(Constant::getConstArray($setting['items']) as $key => $val)
                                        <p>
                                            @php($setting['is_first_check'] = $setting['is_first_check'] ?? true)
                                            {{Form::radio("question[{$q_cd}]",$key,$setting['is_first_check']?($loop->first?true:false):null,['id'=>"question_{$q_cd}_{$key}"])}}
                                            <label class="radio-inline" for="{{"question_{$q_cd}_{$key}"}}">{{$val}}</label>
                                        </p>
                                    @endforeach
                                </div>
                                @break
                                @case(config('const.enquete.form.file.id'))
                                @break
                            @endswitch
                        </fieldset>
                        {{Form::form_line_error("question[{$q_cd}]")}}
                    </div>
                @endforeach

                <div class="btn-submit-question">
                    <p class="submit_question">
                        <button type="submit">送信</button>
                    </p>
                </div>

                {{ Form::hidden('chat_id') }}
                {{ Form::hidden('key',$enquete_key) }}
                {{ Form::hidden('form_hash',$form_hash) }}

                {{ Form::close() }}

                <div class="thanks-question-txt">
                    <p>ご協力ありがとうございました。</p>
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="copy-right">
                <div class="container">
                    <p>{{__('bot.const.bot_copyright')}}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_body')
    <script>

        window.CHAT_BOT = {
            route: @json($route),
            ua_status: @json(__('const.useragent.status')),
        };

        var is_web = {{$is_web ? 'true' : 'false'}};
        var store = {{session('store',false) ? 'true' : 'false'}};
        var id = localStorage.getItem('chat_bot_key');

        if (!is_web && !store) {
            var close_logged = false;
            $(window).on(base.user.getCloseEvent(), function (e) {
                if (close_logged) return false;
                close_logged = true;
                base.user.userLog(id, CHAT_BOT.route.user_log, CHAT_BOT.ua_status.enquete_close.id, true, {{$channel}});
            });
        }

        $(function () {
            //ローカルストレージからchat_idを取得
            if (is_web) {
                //WEB側
                if (id == null) {
                    //何らかの原因でchat_idが取得できない
                    alert("チャットIDが取得できませんでした。\nOKを押すとフォームを閉じます。");
                    window.close();
                }
            } else {
                //WEB以外
                var wid = '{{ $sns_chat_id }}';
                if (id == null) {
                    localStorage.setItem('chat_bot_key', wid);
                    id = wid;
                }
                //WEB以外ロード時アンケート
                if (!store) {
                    base.user.userLog(id, CHAT_BOT.route.user_log, CHAT_BOT.ua_status.enquete_load.id, false, {{$channel}});
                }

            }

            //フォームにセットする
            $('[name="chat_id"]').val(id);
            $('#form_questionCheck').submit(function (e) {
                $(':submit').prop('disabled', true);
            });

        });

        //登録後
        if (store) {
            if (is_web) {
                alert(@json(__('bot.enquete.messages.send_complete')));
                window.close();
            } else {
                alert(@json(__('bot.enquete.messages.send_complete_sns')));
                $(':submit,:input').prop('disabled', true);
            }
        }
    </script>
@endsection