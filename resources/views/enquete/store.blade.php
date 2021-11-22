<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script> window.Laravel = {!! json_encode(['apiToken' => \Auth::user()->api_token ?? null]) !!};</script>
</head>
<body>
<h1>{{$form_setting['title']}}</h1>

<strong>登録完了</strong>

<a href="javascript:window.close();">閉じる</a>

</body>
<script src="{{ asset(mix('/js/app.js')) }}"></script>
<script>
</script>
</html>