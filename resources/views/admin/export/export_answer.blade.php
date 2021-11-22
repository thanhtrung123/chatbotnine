<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>回答できた数</th>
            <th>回答できなかった数</th>
            <th>合計</th>
        </tr>
        </thead>
        <tbody>
                <tr>
                    <td>回答率</td>
                    <td>{{ $answer_data['count_answer'] ?? 0 }}</td>
                    <td>{{ $answer_data['count_no_answer'] ?? 0 }}</td>
                    <td>{{ (int) ($answer_data['count_answer'] ?? 0) + (int) ($answer_data['count_no_answer'] ?? 0) }}</td>
                </tr>
        </tbody>
    </table>
    <p>&nbsp;</p>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>解決した</th>
            <th>解決しなかった</th>
            <th>未回答</th>
            <th>合計</th>
        </tr>
        </thead>
        <tbody>
                <tr>
                    <td>回答率</td>
                    <td>{{  ($answer_data['count_answer_handle'] ?? 0) }}</td>
                    <td>{{  ($answer_data['count_answer_no_handle'] ?? 0) }}</td>
                    <td>{{  ($answer_data['count_answer_yet_handle'] ?? 0) }}</td>
                    <td>{{  (int) ($answer_data['count_answer_handle'] ?? 0) + (int) ($answer_data['count_answer_no_handle'] ?? 0) + (int) ($answer_data['count_answer_yet_handle'] ?? 0) }}</td>
                </tr>
        </tbody>
    </table>
</body>
</html>