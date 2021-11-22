<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <p>集計条件指定</p>
    <table border = "1" width = "100%">
        <tr>
            <td>集計期間</td>
            <td>{{ ($params['date_s'] ?? NULL) ?: '指定なし' }}</td>
            <td>{{ ($params['date_e'] ?? NULL) ?: '指定なし' }}</td>
        </tr>
    </table>
    <p>詳細条件</p>
    <table border = "1" width = "100%">
        <tr>
            <td>集計チャネル</td>
            @php
                $channel_list = config('const.bot.channel');
                $channel_name = '';
                foreach ($channel_list as $channel_data) {
                    if ($channel_data['id'] == ($params['channel'] ?? NULL)) {
                        $channel_name = $channel_data['name'];
                    }
                }
            @endphp
            <td>{{  $channel_name ?: '指定なし' }}</td>
        </tr>
        <tr>
            <td>集計除外IP</td>
            <td>{{ ($params['ip'] ?? NULL) ?: '指定なし' }}</td>
        </tr>
    </table>
</body>
</html>