<!DOCTYPE html>
<html>
<head>
    <title>@yield('pageTitle')</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="author" content="{{env('CLIENT_NAME')}}">
    <meta name="robots" content="noindex,nofollow">
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="{{ asset(mix('/js/ie9.js')) }}"></script>
    <![endif]-->
    @include('layouts.parts.common_config')
    <link rel="icon" href="{{asset('img/favicon/favicon.ico')}}" type="image/x-icon"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="apple-touch-icon" href="{{asset('img/favicon/apple-touch-icon.png')}}"/>
    <link rel="apple-touch-icon-precomposed" href="{{asset('img/favicon/apple-touch-icon.png')}}"/>
    <link rel="stylesheet" href="{{ asset(mix('/css/main.css')) }}" type="text/css" media="screen"/>
    <link rel="stylesheet" href="{{ asset(mix('css/speech-to-text.css')) }}" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.1/css/font-awesome.min.css">
    {{--  ↓サジェスト用仮スタイル  --}}
    <style>
        .autocomplete {
            background: white;
            z-index: 1000;
            font: 14px/22px "-apple-system", BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow: auto;
            box-sizing: border-box;
            border: 1px solid rgba(50, 50, 50, 0.6);
            position: fixed;
            height: auto;
            width: 100%;
            left: 0px;
            max-height: 200px;
            bottom: 52px;
            text-indent: 5px;
        }

        .autocomplete li {
            cursor: pointer;
        }

        .autocomplete li.selected {
            background-color: #0e90d2;
        }

        .autocomplete li:hover {
            background-color: #6dc2e2;
        }
    </style>
    {{--  ↑サジェスト用仮スタイル  --}}
</head>
<!-- canvas -->
<div id="rec_canvas" style="display:none; z-index: 20; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <canvas id="canvas" style="width:320px; height:120px; border:solid 1px #999999; background-color: black"></canvas>
</div>
<!-- The Modal -->
<div id="emptyAudioModal" class="modal none">
    <!-- Modal title -->
    <div class="modal-header">
        <div class="content-header">
            <span>{{__('bot.const.bot_dialog_alert_title')}}</span>
            <i class="fa fa-times closeModal" aria-hidden="true"></i>
        </div>
    </div>
    <!-- Modal content -->
    <div class="modal-content">
        <p>
            {{__('bot.const.bot_dialog_alert_voiceapi_fail')}}
        </p>
    </div>
    <div class="modal-footer">
        <button class="closeModalAudio closeModal">OK</button>
    </div>
</div>
<!-- The Modal Loading -->
<div id="loadingAudioModal" class="modal none">
    <p>{{__(config('bot.const.bot_dialog_voice_api_loading'))}}</p>
    <img src="{{asset('img/images/loading.gif')}}" alt="loading" width="150" height="80">
</div>
<body>
@yield('content')
<script>window.CLOSE_UA = @json(output_config_json('useragent.check_close'))</script>
<script src="{{ asset(mix('/js/user.js')) }}"></script>
<script src="{{ asset(mix('/js/user_bot.js')) }}"></script>
<script>
    var _chaq = _chaq || [];
    _chaq['_accountID'] = 1437;
    (function (D, s) {
        var ca = D.createElement(s)
            , ss = D.getElementsByTagName(s)[0];
        ca.type = 'text/javascript';
        ca.async = !0;
        ca.setAttribute('charset', 'utf-8');
        var sr = 'https://st.aibis.biz/aibis.js';
        ca.src = sr + '?' + parseInt((new Date) / 60000);
        ss.parentNode.insertBefore(ca, ss);
    })(document, 'script');
</script>
</body>
</html>
