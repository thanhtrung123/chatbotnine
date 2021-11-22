@extends('layouts.admin')
@section('pageTitle', '裏ツール 真理表変換')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        {{ Form::open(['url'=>route('admin.tools.truth_morph'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}
                                        <label><input type="checkbox" name="qna" value="1" {{$qna ? 'checked' : ''}}/>QnA用変換にする</label>
                                        {{ Form::form_textarea('question','変換対象文章',true,['required'=>true,'rows'=>5]) }}
                                        <div class="form-group" data-confirm="">
                                            <label for="name" class="col-md-4 control-label">変換後文章</label>
                                            <div class="col-md-8 ">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        {{ $truth }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit">変換</button>
                                            </div>
                                        </div>

                                        {{ Form::close() }}
                                        <hr>

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
