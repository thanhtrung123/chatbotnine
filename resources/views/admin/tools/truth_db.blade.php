@extends('layouts.admin')
@section('pageTitle', '裏ツール 真理表')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>@yield('pageTitle')</h3>
            <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">一覧変更</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <a class="btn btn-default" href="{{route('admin.tools.truth_db',['mode'=>1])}}">全体</a>
                                    <a class="btn btn-default" href="{{route('admin.tools.truth_db',['mode'=>2])}}">ワード毎</a>
                                    <a class="btn btn-default" href="{{route('admin.tools.truth_db',['mode'=>3])}}">API_ID毎</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">一覧</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    @php
                                    $table = [
                                    'setting' => ['displayLength' => 50,'scrollX'=>true],
                                    ];
                                    @endphp
                                    <table class="table" id="truth_table" data-tables='@json($table)'>
                                        <thead>
                                            <tr>
                                                @if($mode=='1')
                                                <th>API_ID</th>
                                                <th>ワード</th>
                                                <th>置換ワード</th>
                                                @elseif($mode=='2')
                                                <th>カウント</th>
                                                <th>比率</th>
                                                <th>ワード</th>
                                                <th>API_ID</th>
                                                @else
                                                <th>API_ID</th>
                                                <th>カウント</th>
                                                <th>ワード</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tt_data as $row)
                                            <tr>
                                                @if($mode=='1')
                                                <td>{{$row['api_id']}}</td>
                                                <td>{{$row['word']}}</td>
                                                <td>{{$row['replace_word']}}</td>
                                                @elseif($mode=='2')
                                                <td>{{$row['cnt']}}</td>
                                                <td>{{$row['rate']}}</td>
                                                <td>{{$row['word']}}</td>
                                                <td>{{$row['api_ids']}}</td>
                                                @else
                                                <td>{{$row['api_id']}}</td>
                                                <td>{{$row['cnt']}}</td>
                                                <td>{{$row['words']}}</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
