@extends('layouts.admin')
@section('pageTitle', __('固有名詞') . ' 修正')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ Form::open(['url'=>route('admin.proper_noun.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}
                                <div class="row">
                                    <div class="col-md-10" id="confirm_form_area">
                                        {{ Form::form_text('word',__('固有名詞'),true,['required'=>true,'autofocus'=>true]) }}
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
                                                <a class="btn btn-default btn-block" href="{{ route('admin.proper_noun.index',['r'=>1]) }}">戻る</a>
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
