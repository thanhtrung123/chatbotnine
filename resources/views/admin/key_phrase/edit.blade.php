@extends('layouts.admin')
@section('pageTitle', __('キーフレーズ') . ' 修正')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ Form::open(['url'=>route('admin.key_phrase.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}
                                <div class="row">
                                    <div class="col-md-10" id="confirm_form_area">
                                        {{ Form::form_text('word',__('キーフレーズ'),true,['required'=>true,'autofocus'=>true]) }}
                                        {{ Form::form_text('replace_word','置換後文字',true,[]) }}
                                        {{ Form::form_text('priority','優先度',true,[]) }}
                                        {{ Form::form_radio('disabled','状態',$statuses,true) }}
                                    </div>
                                </div>
                                <div class="row top-buf10 bottom-buf15">
                                    <div class="col-md-12">
                                        @php
                                            $dtable = ['setting'=>['scrollX'=>true,'order'=>[0,'asc']]];
                                        @endphp
                                        <table class="table" data-tables='@json($dtable)' id="dtable_key_phrase">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>質問文</th>
                                                <th>{{ __('キーフレーズ') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($learning_data as $row)
                                                <tr>
                                                    <td>{{ $row['api_id'] }}</td>
                                                    <td>{{ $row['question'] }}</td>
                                                    <td>{!! implode(' ',$row['words_disp']) !!}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">確認</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.key_phrase.index',['r'=>1]) }}">戻る</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isConfirm)
        {{ Form::form_confirm_script('entry_form','update',['confirm_form_area'])  }}
    @endif
@endsection
