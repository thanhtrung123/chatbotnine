@extends('layouts.admin')
@section('pageTitle', __('admin.header.学習データ').__('admin.登録'))
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
                                        {{ Form::open(['url'=>route('admin.learning.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form']) }}

                                        {{ Form::form_select('category_id',__('admin.learning.カテゴリ'), $category_data,true,['class'=>'select2']) }}
                                        {{ Form::form_textarea('question',__('admin.learning.回答文章'),true,['required'=>true,'autofocus'=>true,'rows'=>3]) }}
                                        {{ Form::form_textarea('answer',__('admin.learning.回答文章'),true,['required'=>true,'rows'=>6]) }}
                                        {{ Form::form_text('metadata',__('admin.learning.メタデータ(仮)')) }}

                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.learning.index') }}">{{__('admin.戻る')}}</a>
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
