@extends('layouts.admin')
@section('pageTitle',  __('admin.header.固有名詞').__('admin.登録'))
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        {{ Form::open(['url'=>route('admin.proper_noun.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}
                                        {{ Form::form_text('word',__('admin.proper_noun.固有名詞'),true,['required'=>true,'autofocus'=>true]) }}
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.proper_noun.index') }}">{{__('admin.戻る')}}</a>
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
        </div>
    </div>
    @if($isConfirm)
        {{ Form::form_confirm_script('entry_form')  }}
    @endif
@endsection
