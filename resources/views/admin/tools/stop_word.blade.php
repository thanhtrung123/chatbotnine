@extends('layouts.admin')
@section('pageTitle', '裏ツール ストップワード')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>@yield('pageTitle')</h3>
            <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">ストップワード設定</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::open(['url'=>route('admin.tools.stop_word'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                    @if(!empty($set_word))
                                    <div class="row">
                                        @if($set_word['success'])
                                        <div class="col-md-10 col-md-offset-1" style="font-weight: bold;color: darkblue;">「{{$set_word['word']}}」をストップワードとして登録しました。</div>
                                        @else
                                        <div class="col-md-10 col-md-offset-1" style="font-weight: bold;color: darkred;">「{{$set_word['word']}}」はストップワードとして登録できません。</div>
                                        @endif
                                    </div>
                                    @endif

                                    {{ Form::form_text('word','ストップワード',true,['required'=>true]) }}

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-block" type="submit">設定</button>
                                        </div>
                                    </div>

                                    {{ Form::close() }}
                                    <hr>
                                    @php
                                    $confirm = ['message'=>"ストップワードを削除しても、真理表には反映されません。\n反映する場合、真理表の再生成を行ってください。"];
                                    @endphp
                                    
                                    <a href="{{route('admin.tools.stop_word',['clear'=>1])}}" data-button-confirm='@json($confirm)'>ストップワードを全て削除</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">ストップワード一覧</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table" >
                                        <tr>
                                            <th>ストップワード</th>
                                            <th>操作</th>
                                        </tr>
                                        @foreach($sw_data as $row)
                                        <tr>
                                            <td>{{$row['word']}}</td>
                                            <td>
                                                <a href="{{route('admin.tools.stop_word',['clear'=>1,'word'=>$row['word']])}}" data-button-confirm='@json($confirm)'>削除</a>
                                            </td>
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
