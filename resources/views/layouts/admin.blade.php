<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle') - {{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    @yield('cssfiles')
    <script> window.Laravel = {!! json_encode([
        'apiToken' => \Auth::user()->api_token ?? null,
        'route'=>['login'=>route('login')]
    ]) !!};</script>
    @include('layouts.parts.common_config')
    <script src="{{ asset(mix('js/admin.js')) }}"></script>
</head>
<body>
<div id="app">
    @include('layouts.parts.admin_header')
    @include('layouts.parts.admin_flush_message')
    @yield('content')
    @include('layouts.parts.admin_footer')

    @include('layouts.parts.admin_ajax_modal')
    @include('layouts.parts.admin_confirm_modal')
    @include('layouts.parts.admin_delete_modal')
{{--    @include('layouts.parts.admin_csv_import_modal')--}}
    @include('layouts.parts.admin_chat_modal')
    @include('layouts.parts.admin_choice_modal')
    @include('layouts.parts.admin_show_modal')
</div>

<!-- Scripts -->
@include('layouts.parts.admin_direct_error_script')
@yield('jsfiles')
</body>
</html>
