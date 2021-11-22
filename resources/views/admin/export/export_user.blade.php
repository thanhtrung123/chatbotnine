<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>ユニークユーザー数</th>
            <th>合計ユーザー数</th>
            <th>トーク数（期間合計）</th>
            <th>1会話平均</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>期間</td>
                <td>{{ $data_user['user_unique'] }}</td>
                <td>{{ $data_user['user_date'] }}</td>
                <td>{{ $data_user['user_talk']  }}</td>
                <td>{{ ($data_user['user_date'] > 0) ? round($data_user['user_talk']/$data_user['user_date'], 2) : 0 }}</td>
            </tr>
            <tr>
                <td>時間</td>
                <td>{{ $data_user['user_unique'] }}</td>
                <td>{{ $data_user['user_hour']  }}</td>
                <td>{{ $data_user['user_talk_hour'] }}</td>
                <td>{{ ($data_user['user_hour'] > 0) ? round($data_user['user_talk_hour']/$data_user['user_hour'], 2) : 0 }}</td>
            </tr>
            <tr>
                <td>曜日</td>
                <td>{{ $data_user['user_unique'] }}</td>
                <td>{{ $data_user['user_day_of_week'] }}</td>
                <td>{{ $data_user['user_talk_day_of_week'] }}</td>
                <td>{{ ($data_user['user_day_of_week'] > 0) ? round($data_user['user_talk_day_of_week']/$data_user['user_day_of_week'], 2) : 0 }}</td>
            </tr>
        </tbody>
    </table>
    <table>
        <thead>
        <tr>
            <th>日付</th>
            <th>利用者数</th>
            <th>トーク数</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data_date['date_list'] as $key => $date)
                <tr>
                    <td>{{  $date }}</td>
                    <td>{{ $data_date['date_data_users'][$key] }}</td>
                    <td>{{ $data_date['date_data_talk'][$key]  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @php
        $page_row = count($data_date['date_list']) + 6;
        $page_new = (int) $page - 1;
        if ($page_row <= $page_new) {
            $page_last = $page_new - $page_row;
            for ($i = 0; $i < $page_last; $i++) {
                echo '<p>&nbsp;</p>';
            }
        }
        
        if ($page < $page_row) {
            $page_last = $page - ($page_row - floor($page_row/$page_new)*$page_new);
            for ($i = 0; $i < $page_last; $i++) {
                echo '<p>&nbsp;</p>';
            }
        }
    @endphp
    <!-- @if (count($data_date['date_list']) < 14)
        @php
            $maxCount = 14 - count($data_date['date_list']);
            for ($i = 0; $i < $maxCount; $i++) {
                echo '<p>&nbsp;</p>';
            }
        @endphp
    @endif -->
    <table>
        <thead>
        <tr>
            <th>時間</th>
            <th>利用者数</th>
            <th>トーク数</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data_hour['hour_list'] as $key => $hour)
                <tr>
                    <td>{{  $hour }}</td>
                    <td>{{ $data_hour['hour_data_users'][$key] }}</td>
                    <td>{{ $data_hour['hour_data_talk'][$key]  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @php
        $page_row = count($data_hour['hour_list']) + 1;
        $page_new = (int) $page - 1;
        if ($page_row < $page_new) {
            $page_last = $page_new - $page_row;
            for ($i = 0; $i < $page_last; $i++) {
                echo '<p>&nbsp;</p>';
            }
        } else {
            $page_last = $page - ($page_row - floor($page_row/$page_new)*$page_new);
            for ($i = 0; $i < $page_last; $i++) {
                echo '<p>&nbsp;</p>';
            }
        }
    @endphp
    <table>
        <thead>
        <tr>
            <th>曜日</th>
            <th>利用者数</th>
            <th>トーク数</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data_day['day_of_week'] as $key => $day)
                <tr>
                    <td>{{  $day }}</td>
                    <td>{{ $data_day['data_day_of_week'][$key] }}</td>
                    <td>{{ $data_day['data_talk_day_of_week'][$key]  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>