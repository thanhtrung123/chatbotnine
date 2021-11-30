@extends('layouts.admin')
@section('pageTitle', __('admin.header.キーフレーズ').__('admin.登録'))
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
                                        {{ Form::open(['url'=>route('admin.key_phrase.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                        {{ Form::form_text('word',__('admin.key_phrase.キーフレーズ'),true,['required'=>true,'autofocus'=>true]) }}
                                        {{ Form::form_text('replace_word',__('admin.key_phrase.置換後文字'),true,[]) }}
                                        {{ Form::form_text('priority',__('admin.key_phrase.優先度'),true,['value'=>0]) }}
                                        {{--                                        {{ Form::form_radio('status','状態',$statuses,true) }}--}}
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.key_phrase.index') }}">{{__('admin.戻る')}}</a>
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
