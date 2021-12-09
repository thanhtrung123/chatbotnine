@extends('layouts.admin')
@section('pageTitle', '裏ツール APIデータ')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <a href="{{route('admin.tools.index')}}"></a>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="row bottom-buf15">
                                            <div class="col-md-2"><b>Runtime Version </b></div>
                                            <div class="col-md-6" style="text-align: left;">Current: <b>{{$version[0]}}</b> lastStable:<b>{{$version[1]}}</b></div>
                                        </div>

                                        <table class="table">
                                            <tr>
                                                <th>ID</th>
                                                <th>{{__('admin.learning.question_text')}}</th>
                                                <th>{{(config('bot.api.answer_is_id')) ? 'API_ID' : '回答'}}</th>
                                            </tr>
                                            @foreach($api_data as $row)
                                                <tr>
                                                    <td>{{$row['id']}}</td>
                                                    <td>{{implode('<br>',$row['questions'])}}</td>
                                                    @if(config('bot.api.answer_is_id'))
                                                        <td><a href="{{route('admin.learning.edit.api',['api_id'=>$row['answer']])}}" target="_blank">{{$row['answer']}}</a></td>
                                                    @else
                                                        <td>{{$row['answer']}}</td>
                                                    @endif
                                                    {{--                                                    <td>{{$row['metadata']}}</td>--}}
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
