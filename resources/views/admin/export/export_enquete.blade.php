<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    @php
        $num = 1;
    @endphp
    @foreach ($result_enquete_combine as $key => $enquete_data)
        <p style="font-size: 14px;font-weight: bold;">{{ 'Q' . $num . ($enquete_data['question_name'] ?? NULL) }}</p>
        <table>
            <thead>
            <tr>
                @foreach (($enquete_data['item_data'] ?? []) as $enquete_tile)
                <th>{{ $enquete_tile }}</th>
                @endforeach
                <th>合計</th>
            </tr>
            </thead>
            <tbody>
                    <tr>
                        @php
                            $total = 0;
                        @endphp
                        
                        @foreach (($enquete_data['item_value'] ?? []) as $enquete_value)
                            @php
                                $total = (int) $total + (int) $enquete_value;
                            @endphp
                            <td>{{  $enquete_value }}</td>
                        @endforeach
                        <td>{{ $total }}</td>
                    </tr>
            </tbody>
        </table>
        @php
            $num++;
        @endphp
		@if (($key + 1) % 2 != 0)
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		@else
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		@endif
    @endforeach
</body>
</html>