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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="{{ asset(mix('/js/ie9.js')) }}"></script>
    <![endif]-->
    @include('layouts.parts.common_config')
    <link rel="icon" href="{{asset('img/favicon/favicon.ico')}}" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="{{asset('img/favicon/apple-touch-icon.png')}}"/>
    <link rel="apple-touch-icon-precomposed" href="{{asset('img/favicon/apple-touch-icon.png')}}"/>
    <link rel="stylesheet" href="{{ asset(mix('/css/main.css')) }}" type="text/css" media="screen"/>
</head>
<body>
@yield('content')
<script>window.CLOSE_UA = @json(output_config_json('useragent.check_close'))</script>
<script src="{{ asset(mix('/js/user.js')) }}"></script>
<script src="{{ asset(mix('/js/user_enquete.js')) }}"></script>
@yield('script_body')
</body>
</html>