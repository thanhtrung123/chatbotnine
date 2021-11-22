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
                            <div class="panel-heading">質問文→真理表（リアルタイム変換）一覧
                                @if(request()->get('qna'))
                                    <a class="small" href="?qna=">真理表用を見る</a>
                                @else
                                    <a class="small" href="?qna=1">QnA用を見る</a>
                                @endif
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <table class="table">
                                            <tr>
                                                <th>API_ID</th>
                                                <th>質問文</th>
                                                <th>真理表</th>
                                            </tr>
                                            @foreach($tt_data as $row)
                                                <tr>
                                                    <td>{{$row['api_id']}}</td>
                                                    <td>{{$row['question']}}</td>
                                                    <td>{!! nl2br($row['truth']) !!}</td>
                                                </tr>
                                            @endforeach
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
